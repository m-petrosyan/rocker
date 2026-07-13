<?php

namespace App\Telegram;

use App\Services\TelegramPostImportService;
use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegraphHandler extends WebhookHandler
{
    use TelegramDefaultMethodsHandler,
        TelegramEventsHandler,
        TelegramMenuHandler,
        TelegramSettingsHandler;

    public function handle(Request $request, TelegraphBot $bot): void
    {
        $chatType = $request->input('callback_query.message.chat.type')
            ?? $request->input('message.chat.type')
            ?? $request->input('channel_post.chat.type')
            ?? null;

        Log::info('chatType', [$chatType]);

        if ($chatType === 'private') {
            parent::handle($request, $bot);

            return;
        }

        // Обработка постов из групп/каналов через TelegramPostImportService
        $payload = $request->input('channel_post') ?? $request->input('message');

        if ($payload) {
            try {
                $service = app(TelegramPostImportService::class);
                $event = $service->import($payload);

                if ($event) {
                    Log::info('Event created from group post via webhook', [
                        'event_id' => $event->id,
                        'chat_type' => $chatType,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Error processing group post via webhook', [
                    'error' => $e->getMessage(),
                    'chat_type' => $chatType,
                ]);
            }
        } else {
            Log::info('Group/channel update without message payload', ['chat_type' => $chatType]);
        }
    }

    public function start(): void
    {
        $this->reply('🤘');
        Log::info(111);
        $this->chat->action(ChatActions::TYPING)->send();
        Log::info(222);

        $this->reply('👋 Welcome to Rocker Bot!');
        Log::info(333);
        Log::info('User ID:', ['id' => $this->chat?->user?->id]);

        $this->get_countries();
    }

    public function sendMessageWithButton(string $messageText, array $buttons, ?int $messageId = null): void
    {
        if ($messageId) {
            $this->chat->replaceKeyboard(
                $messageId,
                Keyboard::make()->buttons($buttons)
            )->send();

            return;
        }

        $msg = $this->chat->message($messageText)
            ->keyboard(Keyboard::make()->buttons($buttons))
            ->send();

        Cache::store('redis')->put("chat:{$this->chat->chat_id}:last_menu_id", $msg->telegraphMessageId(), 432000);
    }

    public function prepareMessageParams(string $chatId, ?int $currentMessageId): ?int
    {
        $cacheKey = "chat:{$chatId}:last_menu_id";
        $lastMessageId = Cache::store('redis')->get($cacheKey);

        if ($lastMessageId && $lastMessageId + 2 >= $currentMessageId) {
            return $lastMessageId;
        }

        Cache::store('redis')->put($cacheKey, $currentMessageId, 432000);
        if ($lastMessageId) {
            $this->chat->deleteMessage($lastMessageId)->send();
        }

        return null;
    }
}
