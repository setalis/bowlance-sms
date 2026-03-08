<?php

namespace App\Console\Commands;

use App\Services\TelegramVerifyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramPollCommand extends Command
{
    protected $signature = 'telegram:poll';

    protected $description = 'Запустить Telegram long polling для локальной разработки (вместо webhook)';

    public function handle(TelegramVerifyService $telegramService): void
    {
        $token = config('telegram.bot_token');

        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN не настроен в .env');

            return;
        }

        $this->info('Telegram polling запущен. Нажмите Ctrl+C для остановки.');
        $this->info('Бот: @'.config('telegram.bot_username'));

        $offset = 0;
        $apiBase = "https://api.telegram.org/bot{$token}";

        // Удалить вебхук, чтобы не конфликтовал с polling
        Http::post("{$apiBase}/deleteWebhook");

        while (true) {
            try {
                $response = Http::timeout(35)->get("{$apiBase}/getUpdates", [
                    'offset' => $offset,
                    'timeout' => 30,
                    'allowed_updates' => json_encode(['message', 'callback_query']),
                ]);

                if (! $response->successful()) {
                    $this->warn('Ошибка API: '.$response->status());
                    sleep(5);

                    continue;
                }

                $data = $response->json();

                if (! ($data['ok'] ?? false)) {
                    $this->warn('Telegram API error: '.($data['description'] ?? 'unknown'));
                    sleep(5);

                    continue;
                }

                foreach ($data['result'] as $update) {
                    $offset = $update['update_id'] + 1;

                    $type = isset($update['message']) ? 'message' : (isset($update['callback_query']) ? 'callback' : 'other');
                    $this->line('['.now()->format('H:i:s').'] Update #'.$update['update_id'].' ('.$type.')');

                    $telegramService->handleWebhookUpdate($update);
                }
            } catch (\Exception $e) {
                $this->error('Ошибка: '.$e->getMessage());
                sleep(5);
            }
        }
    }
}
