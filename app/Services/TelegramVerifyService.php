<?php

namespace App\Services;

use App\Models\PhoneVerification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramVerifyService
{
    protected string $botToken;

    protected string $botUsername;

    protected string $apiBase;

    public function __construct()
    {
        $this->botToken = config('telegram.bot_token', '');
        $this->botUsername = config('telegram.bot_username', '');
        $this->apiBase = "https://api.telegram.org/bot{$this->botToken}";
    }

    /**
     * Инициировать верификацию через Telegram.
     * Создаёт запись PhoneVerification с channel=telegram и уникальным токеном,
     * возвращает ссылку на бота с этим токеном.
     */
    public function initiateVerification(string $phone): array
    {
        $token = Str::uuid()->toString();
        $requestId = 'tg-'.Str::uuid()->toString();

        PhoneVerification::create([
            'phone' => $phone,
            'channel' => 'telegram',
            'request_id' => $requestId,
            'telegram_token' => $token,
            'expires_at' => now()->addMinutes(10),
        ]);

        $telegramLink = "https://t.me/{$this->botUsername}?start={$token}";

        return [
            'success' => true,
            'request_id' => $requestId,
            'telegram_link' => $telegramLink,
        ];
    }

    /**
     * Обработать входящее обновление от Telegram webhook.
     */
    public function handleWebhookUpdate(array $update): void
    {
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    /**
     * Обработать входящее сообщение.
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        if (str_starts_with($text, '/start ')) {
            $token = trim(substr($text, 7));
            $this->handleStartWithToken($chatId, $token);
        } elseif ($text === '/start') {
            $this->sendMessage($chatId, "Привет! Я бот для верификации телефона Bowlance.\n\nПерейдите по ссылке с сайта Bowlance, чтобы подтвердить ваш номер телефона.");
        }
    }

    /**
     * Обработать /start с токеном верификации.
     */
    protected function handleStartWithToken(int $chatId, string $token): void
    {
        $verification = PhoneVerification::where('telegram_token', $token)
            ->where('channel', 'telegram')
            ->first();

        if (! $verification) {
            $this->sendMessage($chatId, '❌ Ссылка недействительна или устарела. Пожалуйста, начните процесс верификации заново на сайте.');

            return;
        }

        if ($verification->verified) {
            $this->sendMessage($chatId, '✅ Этот номер телефона уже подтверждён.');

            return;
        }

        if ($verification->isExpired()) {
            $this->sendMessage($chatId, '⏰ Срок действия ссылки истёк. Пожалуйста, начните процесс верификации заново на сайте.');

            return;
        }

        $phone = $verification->phone;

        $this->sendMessageWithInlineKeyboard(
            $chatId,
            "📱 Подтверждение номера телефона\n\nНомер: *{$phone}*\n\nНажмите кнопку ниже, чтобы получить код подтверждения:",
            [
                [
                    [
                        'text' => '✅ Получить код подтверждения',
                        'callback_data' => "confirm:{$token}",
                    ],
                ],
            ]
        );
    }

    /**
     * Обработать нажатие инлайн-кнопки.
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $callbackQueryId = $callbackQuery['id'];
        $data = $callbackQuery['data'] ?? '';

        $this->answerCallbackQuery($callbackQueryId);

        if (str_starts_with($data, 'confirm:')) {
            $token = substr($data, 8);
            $this->processConfirmation($chatId, $messageId, $token);
        }
    }

    /**
     * Обработать подтверждение номера: сгенерировать и отправить код.
     */
    protected function processConfirmation(int $chatId, int $messageId, string $token): void
    {
        $verification = PhoneVerification::where('telegram_token', $token)
            ->where('channel', 'telegram')
            ->first();

        if (! $verification) {
            $this->sendMessage($chatId, '❌ Запрос верификации не найден. Пожалуйста, начните процесс заново.');

            return;
        }

        if ($verification->verified) {
            $this->sendMessage($chatId, '✅ Этот номер телефона уже подтверждён.');

            return;
        }

        if ($verification->isExpired()) {
            $this->sendMessage($chatId, '⏰ Срок действия запроса истёк. Пожалуйста, начните процесс заново на сайте.');

            return;
        }

        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $verification->update([
            'code' => $code,
            'telegram_chat_id' => $chatId,
        ]);

        $this->editMessageText(
            $chatId,
            $messageId,
            "📱 Ваш код подтверждения:\n\n`{$code}`\n\nВведите этот код на сайте Bowlance.\n\n⏱ Код действителен 10 минут."
        );

        Log::info('Telegram verification code sent', [
            'phone' => $verification->phone,
            'request_id' => $verification->request_id,
            'chat_id' => $chatId,
        ]);
    }

    /**
     * Отправить сообщение с подтверждением успешной верификации в Telegram.
     */
    public function sendConfirmationMessage(int $chatId): void
    {
        $this->sendMessage($chatId, "✅ Ваш номер телефона успешно подтверждён!\n\nСпасибо за использование Bowlance.");
    }

    /**
     * Отправить простое текстовое сообщение.
     */
    protected function sendMessage(int $chatId, string $text): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessage failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Отправить сообщение с инлайн-клавиатурой.
     *
     * @param  array<int, array<int, array{text: string, callback_data: string}>>  $keyboard
     */
    protected function sendMessageWithInlineKeyboard(int $chatId, string $text, array $keyboard): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessageWithInlineKeyboard failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Редактировать текст существующего сообщения.
     */
    protected function editMessageText(int $chatId, int $messageId, string $text): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/editMessageText", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram editMessageText failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ответить на callback-запрос (убрать "загрузку" с кнопки).
     */
    protected function answerCallbackQuery(string $callbackQueryId): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram answerCallbackQuery failed', ['error' => $e->getMessage()]);
        }
    }
}
