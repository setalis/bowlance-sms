<?php

namespace App\Http\Controllers\Cabinet\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Services\PhoneAuthService;
use App\Services\PhoneNormalizer;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected PhoneAuthService $phoneAuthService
    ) {}

    public function create(): View|RedirectResponse|Response
    {
        if (auth()->check()) {
            return redirect()->intended(route('cabinet.dashboard', absolute: false));
        }

        return response()
            ->view('cabinet.auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string', 'digits:6'],
            'request_id' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        try {
            $normalizedPhone = PhoneNormalizer::normalize(trim((string) $request->phone));

            $verification = PhoneVerification::where('request_id', $request->request_id)
                ->where('verified', true)
                ->where('phone', $normalizedPhone)
                ->first();

            if (! $verification) {
                RateLimiter::hit($this->throttleKey($request));

                return response()->json([
                    'success' => false,
                    'message' => 'Верификация не найдена или номер не подтвержден',
                ], 422);
            }

            RateLimiter::clear($this->throttleKey($request));

            $user = $this->phoneAuthService->findOrCreateUser(
                $normalizedPhone,
                null,
                null
            );

            // Авторизуем пользователя
            Auth::login($user, true); // remember = true
            $request->session()->regenerate();

            Log::info('User logged in via SMS verification', [
                'user_id' => $user->id,
                'phone' => $user->phone,
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('cabinet.dashboard', absolute: false),
            ]);
        } catch (\Exception $e) {
            Log::error('Cabinet login error', [
                'error' => $e->getMessage(),
                'phone' => $request->phone,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при входе',
            ], 500);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cabinet.login');
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'phone' => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => (int) ceil($seconds / 60),
            ])],
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('phone')->trim()).'|'.$request->ip());
    }
}
