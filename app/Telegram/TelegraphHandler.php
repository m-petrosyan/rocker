<?php

namespace App\Telegram;

use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegraphHandler extends WebhookHandler
{
    use TelegramSettingsHandler,
        TelegramEventsHandler,
        TelegramMenuHandler,
        TelegramDefaultMethodsHandler;

    public function handle(Request $request, TelegraphBot $bot): void
    {
//        Log::info('handle', ['request' => $request->all()]);
//        Log::info('handle', ['bot' => $bot]);

        $chatType = $request->input('callback_query.message.chat.type')
            ?? $request->input('message.chat.type')
            ?? $request->input('channel_post.chat.type')
            ?? null;
//        Log::info($request);
        Log::info('chatType', [$chatType]);

        if ($chatType === 'private') {
            parent::handle($request, $bot);
        } else {
            Log::info('Group or channel chat detected, ignoring');

            return;
        }
    }


    public function start(): void
    {
        $this->reply("🤘");

        $this->chat->action(ChatActions::TYPING)->send();

        $this->reply("👋 Welcome to Rocker Bot!");

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

        $this->chat->deleteMessage($lastMessageId)->send();

        return null;
    }
}
