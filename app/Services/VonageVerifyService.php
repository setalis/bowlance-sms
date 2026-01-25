<?php

namespace App\Services;

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VonageVerifyService
{
    protected string $apiKey;

    protected string $apiSecret;

    protected string $baseUrl = 'https://api.nexmo.com/v2/verify';

    public function __construct()
    {
        $this->apiKey = config('vonage.api_key');
        $this->apiSecret = config('vonage.api_secret');
    }

    public function sendVerificationCode(string $phone): array
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
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
                'message' => 'Произошла ошибка при отправке кода',
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

            $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
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
            $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
                ->delete("{$this->baseUrl}/{$requestId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Vonage verification cancellation exception', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
