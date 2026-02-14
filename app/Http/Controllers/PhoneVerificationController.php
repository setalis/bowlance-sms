<?php

namespace App\Http\Controllers;

use App\Enums\PhoneVerificationChannel;
use App\Models\PhoneVerification;
use App\Services\PhoneVerification\PhoneVerificationService;
use App\Services\PhoneVerification\TelegramVerificationProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneVerificationController extends Controller
{
    public function __construct(
        protected PhoneVerificationService $verificationService,
        protected TelegramVerificationProvider $telegramVerificationProvider,
    ) {}

    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
            'channel' => 'nullable|in:sms,telegram',
        ], [
            'phone.required' => 'Необходимо указать номер телефона',
            'phone.regex' => 'Неверный формат номера телефона',
            'channel.in' => 'Неверный канал верификации',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;
        $channel = $request->input('channel', PhoneVerificationChannel::Sms->value);

        if (! str_starts_with($phone, '+')) {
            $phone = '+'.ltrim($phone, '0');
        }

        $result = $this->verificationService->send($phone, $channel);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'channel' => $channel,
                'message' => $channel === PhoneVerificationChannel::Telegram->value
                    ? 'Откройте Telegram для подтверждения телефона'
                    : 'Код верификации отправлен на ваш номер',
                'request_id' => $result['request_id'],
                'telegram_url' => $result['telegram_url'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Не удалось отправить код',
        ], 500);
    }

    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string',
            'code' => 'nullable|string|digits:6',
        ], [
            'request_id.required' => 'ID запроса отсутствует',
            'code.digits' => 'Код должен состоять из 6 цифр',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->verificationService->verify(
            $request->request_id,
            $request->code
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'phone' => $result['phone'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    public function status(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $verification = PhoneVerification::where('request_id', $request->request_id)->first();

        if (! $verification) {
            return response()->json([
                'success' => false,
                'message' => 'Запрос верификации не найден',
            ], 404);
        }

        $status = $verification->getAttribute('status') ?? 'pending';

        return response()->json([
            'success' => true,
            'status' => $status,
            'verified' => (bool) $verification->verified,
            'verified_at' => $verification->verified_at,
            'is_expired' => $verification->isExpired(),
        ]);
    }

    public function telegramCallback(Request $request): JsonResponse
    {
        \Log::info('phone.verify.telegram.callback: request received', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'input_keys' => array_keys($request->all()),
            'body_length' => strlen($request->getContent()),
        ]);

        [$token, $tokenFromKey] = $this->extractTelegramCallbackToken($request);
        $token = trim((string) $token);
        foreach (['/start ', 'start ', '/start'] as $prefix) {
            if (str_starts_with($token, $prefix)) {
                $token = trim(substr($token, strlen($prefix)));
                break;
            }
        }
        $token = trim(urldecode($token));
        if ($token === '') {
            \Log::info('phone.verify.telegram.callback: token empty', [
                'keys' => array_keys($request->all()),
                'content_type' => $request->header('Content-Type'),
                'body_preview' => strlen($request->getContent()) > 0 ? substr($request->getContent(), 0, 200) : null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Токен верификации не передан',
            ], 422);
        }

        \Log::info('phone.verify.telegram.callback: token received', [
            'token_from_key' => $tokenFromKey,
            'token_length' => strlen($token),
            'token_preview' => strlen($token) >= 8 ? substr($token, 0, 8).'…'.substr($token, -8) : $token,
        ]);

        $result = $this->telegramVerificationProvider->verifyByTelegramToken(
            $token,
            $request->integer('telegram_user_id')
        );

        if (! $result['success']) {
            \Log::info('phone.verify.telegram.callback: verification failed', [
                'token_length' => strlen($token),
                'message' => $result['message'],
            ]);
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Извлечь токен верификации из запроса бота.
     * Бот должен передать значение start-параметра из ссылки t.me/BotName?start=TOKEN
     * в теле запроса (JSON или form): ключ "token" или "start", или "start_param", "verification_token", "payload".
     *
     * @return array{0: string, 1: string} [токен, ключ откуда взят]
     */
    private function extractTelegramCallbackToken(Request $request): array
    {
        $keys = ['token', 'start', 'start_param', 'verification_token', 'payload', 'data.token', 'data.start'];
        foreach ($keys as $key) {
            $value = $request->input($key);
            if ($value !== null && (string) $value !== '') {
                return [(string) $value, $key];
            }
        }
        $content = $request->getContent();
        if ($request->header('Content-Type') && str_contains($request->header('Content-Type'), 'application/json') && $content !== '') {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                foreach (['token', 'start', 'start_param', 'verification_token', 'payload'] as $k) {
                    if (isset($decoded[$k]) && (string) $decoded[$k] !== '') {
                        return [(string) $decoded[$k], $k];
                    }
                }
                if (isset($decoded['data']) && is_array($decoded['data'])) {
                    foreach (['token', 'start'] as $k) {
                        if (isset($decoded['data'][$k]) && (string) $decoded['data'][$k] !== '') {
                            return [(string) $decoded['data'][$k], 'data.'.$k];
                        }
                    }
                }
            }
        }

        return ['', ''];
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $cancelled = $this->verificationService->cancel($request->request_id);

        return response()->json([
            'success' => $cancelled,
            'message' => $cancelled ? 'Запрос верификации отменён' : 'Не удалось отменить запрос',
        ]);
    }
}
