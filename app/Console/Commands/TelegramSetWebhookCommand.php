<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook
                            {--url= : URL вебхука (по умолчанию: APP_URL + /phone/verify/telegram/webhook)}';

    protected $description = 'Регистрирует URL вебхука в Telegram (бот будет присылать сюда обновления)';

    public function handle(): int
    {
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            $this->error('Задайте TELEGRAM_BOT_TOKEN в .env');

            return self::FAILURE;
        }

        $webhookUrl = $this->option('url') ?? rtrim(config('app.url'), '/').'/phone/verify/telegram/webhook';
        $this->line("URL вебхука: <comment>{$webhookUrl}</comment>");

        $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";

        $response = Http::timeout(10)->get($apiUrl, ['url' => $webhookUrl]);

        if (! $response->successful()) {
            $this->error('Ошибка: '.$response->body());

            return self::FAILURE;
        }

        $data = $response->json();
        if (! ($data['ok'] ?? false)) {
            $description = $data['description'] ?? 'unknown';
            $this->error('Telegram вернул ошибку: '.$description);
            if (str_contains($description, 'resolve host') || str_contains($description, 'Name or service not known')) {
                $this->newLine();
                $this->warn('Домен в URL не резолвится из интернета (Telegram не может достучаться до вашего сервера).');
                $this->warn('Используйте публичный URL, например:');
                $this->line('  php artisan telegram:set-webhook --url=https://ВАШ_ДОМЕН/phone/verify/telegram/webhook');
                $this->line('  (ВАШ_ДОМЕН — домен с реальным DNS, не localhost и не запись только из hosts.)');
            }

            return self::FAILURE;
        }

        $this->info("Вебхук установлен: {$webhookUrl}");

        return self::SUCCESS;
    }
}
