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
        }
    }

    /**
     * Обработать входящее сообщение.
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];

        if (isset($message['contact'])) {
            $this->handleContactMessage($chatId, $message['from']['id'] ?? null, $message['contact']);

            return;
        }

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
     * Сохраняет chat_id и просит пользователя поделиться номером телефона.
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

        $verification->update(['telegram_chat_id' => $chatId]);

        $phone = $verification->phone;

        $this->sendMessageWithContactKeyboard(
            $chatId,
            "📱 Подтверждение номера телефона\n\nВы хотите подтвердить номер: *{$phone}*\n\nНажмите кнопку ниже, чтобы поделиться своим номером из Telegram. Он должен совпадать с введённым на сайте."
        );
    }

    /**
     * Обработать контакт, отправленный пользователем через кнопку «Поделиться номером».
     * Сравнивает номер из Telegram с сохранённым и при совпадении выдаёт код.
     */
    protected function handleContactMessage(int $chatId, ?int $fromId, array $contact): void
    {
        // Убедиться, что пользователь делится своим собственным номером, а не чужим контактом
        if (isset($contact['user_id']) && $fromId !== null && $contact['user_id'] !== $fromId) {
            $this->sendMessageRemoveKeyboard($chatId, '❌ Пожалуйста, поделитесь своим собственным номером телефона, нажав на кнопку ниже.');

            return;
        }

        $verification = PhoneVerification::where('telegram_chat_id', $chatId)
            ->where('channel', 'telegram')
            ->where('verified', false)
            ->orderByDesc('created_at')
            ->first();

        if (! $verification) {
            $this->sendMessageRemoveKeyboard($chatId, '❌ Активный запрос верификации не найден. Пожалуйста, начните процесс заново на сайте.');

            return;
        }

        if ($verification->isExpired()) {
            $this->sendMessageRemoveKeyboard($chatId, '⏰ Срок действия запроса истёк. Пожалуйста, начните процесс заново на сайте.');

            return;
        }

        $telegramPhone = $this->normalizePhone($contact['phone_number']);
        $storedPhone = $this->normalizePhone($verification->phone);

        if ($telegramPhone !== $storedPhone) {
            $this->sendMessageRemoveKeyboard(
                $chatId,
                "❌ Номер телефона в Telegram не совпадает с введённым на сайте.\n\nОжидался номер: *{$verification->phone}*\n\nПожалуйста, используйте Telegram-аккаунт с этим номером или введите правильный номер на сайте."
            );

            Log::warning('Telegram phone mismatch during verification', [
                'request_id' => $verification->request_id,
                'stored_phone' => $verification->phone,
                'telegram_phone' => '+'.$telegramPhone,
            ]);

            return;
        }

        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $verification->update(['code' => $code]);

        $this->sendMessageRemoveKeyboard(
            $chatId,
            "✅ Номер подтверждён!\n\n📱 Ваш код подтверждения:\n\n`{$code}`\n\nВведите этот код на сайте Bowlance.\n\n⏱ Код действителен 10 минут."
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
     * Нормализовать номер телефона до цифр (без +, пробелов и т.д.).
     */
    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
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
     * Отправить сообщение с кнопкой «Поделиться номером телефона» (reply keyboard).
     */
    protected function sendMessageWithContactKeyboard(int $chatId, string $text): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'keyboard' => [[['text' => '📱 Поделиться номером телефона', 'request_contact' => true]]],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                ]),
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessageWithContactKeyboard failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Отправить сообщение и убрать reply-клавиатуру.
     */
    protected function sendMessageRemoveKeyboard(int $chatId, string $text): void
    {
        if (empty($this->botToken)) {
            return;
        }

        try {
            Http::timeout(10)->post("{$this->apiBase}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode(['remove_keyboard' => true]),
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessageRemoveKeyboard failed', ['error' => $e->getMessage()]);
        }
    }
}
