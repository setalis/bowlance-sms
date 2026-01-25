<?php

namespace App\Http\Controllers;

use App\Services\VonageVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneVerificationController extends Controller
{
    public function __construct(
        protected VonageVerifyService $verifyService
    ) {}

    public function send(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
        ], [
            'phone.required' => 'Необходимо указать номер телефона',
            'phone.regex' => 'Неверный формат номера телефона',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;

        // Нормализуем номер телефона
        if (! str_starts_with($phone, '+')) {
            $phone = '+'.ltrim($phone, '0');
        }

        $result = $this->verifyService->sendVerificationCode($phone);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Код верификации отправлен на ваш номер',
                'request_id' => $result['request_id'],
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
            'code' => 'required|string|digits:6',
        ], [
            'request_id.required' => 'ID запроса отсутствует',
            'code.required' => 'Необходимо ввести код верификации',
            'code.digits' => 'Код должен состоять из 6 цифр',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->verifyService->verifyCode(
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

        $cancelled = $this->verifyService->cancelVerification($request->request_id);

        return response()->json([
            'success' => $cancelled,
            'message' => $cancelled ? 'Запрос верификации отменён' : 'Не удалось отменить запрос',
        ]);
    }
}
