<?php

namespace App\Services;

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VonageVerifyService
{
    protected string $apiKey;

    protected string $apiSecret;

    protected string $baseUrl = 'https://api.nexmo.com/v2/verify';

    protected bool $testMode;

    public function __construct()
    {
        $this->apiKey = config('vonage.api_key');
        $this->apiSecret = config('vonage.api_secret');
        $this->testMode = config('vonage.test_mode', false);
    }

    public function sendVerificationCode(string $phone): array
    {
        try {
            // Тестовый режим - без реального API
            if ($this->testMode) {
                return $this->sendTestVerificationCode($phone);
            }

            $response = Http::timeout(30)
                ->withBasicAuth($this->apiKey, $this->apiSecret)
                ->post($this->baseUrl, [
                    'brand' => config('app.name'),
                    'workflow' => [
                        [
                            'channel' => 'sms',
                            'to' => $phone,
                        ],
                    ],
                    'locale' => 'ru-ru',
                    'code_length' => 6,
                    'channel_timeout' => 300,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                PhoneVerification::create([
                    'phone' => $phone,
                    'request_id' => $data['request_id'],
                    'expires_at' => now()->addMinutes(5),
                ]);

                return [
                    'success' => true,
                    'request_id' => $data['request_id'],
                ];
            }

            Log::error('Vonage verification request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Не удалось отправить код верификации',
            ];
        } catch (\Exception $e) {
            Log::error('Vonage verification exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Произошла ошибка при отправке кода: '.$e->getMessage(),
            ];
        }
    }

    public function verifyCode(string $requestId, string $code): array
    {
        try {
            $verification = PhoneVerification::where('request_id', $requestId)->first();

            if (! $verification) {
                return [
                    'success' => false,
                    'message' => 'Запрос верификации не найден',
                ];
            }

            if ($verification->verified) {
                return [
                    'success' => true,
                    'message' => 'Номер уже верифицирован',
                ];
            }

            if (! $verification->canAttempt()) {
                return [
                    'success' => false,
                    'message' => 'Превышено количество попыток или истёк срок действия кода',
                ];
            }

            // Тестовый режим - принимаем любой 6-значный код
            if ($this->testMode) {
                return $this->verifyTestCode($verification, $code);
            }

            $response = Http::timeout(30)
                ->withBasicAuth($this->apiKey, $this->apiSecret)
                ->post("{$this->baseUrl}/{$requestId}", [
                    'code' => $code,
                ]);

            $verification->incrementAttempts();

            if ($response->successful()) {
                $verification->markAsVerified();

                return [
                    'success' => true,
                    'message' => 'Номер телефона успешно верифицирован',
                    'phone' => $verification->phone,
                ];
            }

            $data = $response->json();

            Log::warning('Vonage verification check failed', [
                'request_id' => $requestId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $data['title'] ?? 'Неверный код верификации',
            ];
        } catch (\Exception $e) {
            Log::error('Vonage verification check exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Произошла ошибка при проверке кода',
            ];
        }
    }

    public function cancelVerification(string $requestId): bool
    {
        try {
            if ($this->testMode) {
                return true;
            }

            $response = Http::timeout(30)
                ->withBasicAuth($this->apiKey, $this->apiSecret)
                ->delete("{$this->baseUrl}/{$requestId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Vonage verification cancellation exception', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Тестовый режим: генерация фейкового кода верификации
     */
    protected function sendTestVerificationCode(string $phone): array
    {
        $requestId = 'test-'.Str::uuid();
        $testCode = '123456'; // Тестовый код

        PhoneVerification::create([
            'phone' => $phone,
            'request_id' => $requestId,
            'code' => $testCode, // Сохраняем тестовый код
            'expires_at' => now()->addMinutes(5),
        ]);

        Log::info('Test mode: Verification code sent', [
            'phone' => $phone,
            'request_id' => $requestId,
            'code' => $testCode,
        ]);

        return [
            'success' => true,
            'request_id' => $requestId,
            'test_mode' => true,
            'test_code' => $testCode, // Возвращаем код для отладки
        ];
    }

    /**
     * Тестовый режим: проверка кода (принимаем любой 6-значный)
     */
    protected function verifyTestCode(PhoneVerification $verification, string $code): array
    {
        $verification->incrementAttempts();

        // В тестовом режиме принимаем любой 6-значный код
        if (strlen($code) === 6 && is_numeric($code)) {
            $verification->markAsVerified();

            Log::info('Test mode: Code verified', [
                'phone' => $verification->phone,
                'request_id' => $verification->request_id,
                'code' => $code,
            ]);

            return [
                'success' => true,
                'message' => 'Номер телефона успешно верифицирован (тестовый режим)',
                'phone' => $verification->phone,
                'test_mode' => true,
            ];
        }

        return [
            'success' => false,
            'message' => 'Неверный код (в тестовом режиме введите любой 6-значный код)',
        ];
    }
}
