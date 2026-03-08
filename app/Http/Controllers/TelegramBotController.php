<?php

namespace App\Http\Controllers;

use App\Services\TelegramVerifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramBotController extends Controller
{
    public function __construct(
        protected TelegramVerifyService $telegramService
    ) {}

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('telegram.webhook_secret', '');

        if (! empty($secret)) {
            $receivedSecret = $request->header('X-Telegram-Bot-Api-Secret-Token', '');

            if (! hash_equals($secret, $receivedSecret)) {
                return response()->json(['ok' => false], 403);
            }
        }

        $update = $request->all();

        $this->telegramService->handleWebhookUpdate($update);

        return response()->json(['ok' => true]);
    }
}
