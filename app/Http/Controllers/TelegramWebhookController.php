<?php

namespace App\Http\Controllers;

use App\Services\PhoneVerification\TelegramVerificationProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramVerificationProvider $telegramVerificationProvider,
    ) {}

    /**
     * Принимает обновления от Telegram (webhook).
     * При сообщении /start TOKEN проверяет токен верификации и отправляет ответ пользователю в Telegram.
     */
    public function __invoke(Request $request): Response
    {
        $payload = $request->all();
        $message = $payload['message'] ?? null;
        if (! $message) {
            return response('', 200);
        }

        $text = trim($message['text'] ?? '');
        $chatId = $message['chat']['id'] ?? null;
        $fromId = isset($message['from']['id']) ? (int) $message['from']['id'] : null;

        if ($chatId === null || $text === '') {
            return response('', 200);
        }

        if (! str_starts_with($text, '/start')) {
            $this->sendTelegramMessage($chatId, 'Отправьте ссылку с сайта для подтверждения номера телефона.');

            return response('', 200);
        }

        $token = trim(substr($text, 6));
        if ($token === '') {
            $this->sendTelegramMessage($chatId, 'Ссылка не содержит токен. Запросите новую ссылку на сайте.');

            return response('', 200);
        }

        $result = $this->telegramVerificationProvider->verifyByTelegramToken($token, $fromId);
        $this->sendTelegramMessage($chatId, $result['message']);

        return response('', 200);
    }

    private function sendTelegramMessage(int|string $chatId, string $text): void
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            Log::warning('telegram.webhook: TELEGRAM_BOT_TOKEN не задан, ответ не отправлен');

            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        Http::timeout(5)->post($url, [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}
