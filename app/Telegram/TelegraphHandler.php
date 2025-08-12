<?php

namespace App\Telegram;

use App\Models\UserBot;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class TelegraphHandler extends WebhookHandler
{
    public function handle(Request $request, TelegraphBot $bot): void
    {
//        Log::info('handle', ['request' => $request->all()]);

        $chatType = $request->input('callback_query.message.chat.type')
            ?? $request->input('message.chat.type')
            ?? $request->input('channel_post.chat.type')
            ?? null;

//        Log::info('chatType', [$chatType]);

        if ($chatType === 'private') {
            parent::handle($request, $bot);
        } else {
            Log::info('Group or channel chat detected, ignoring');

            return;
        }
    }

    public function handleChatMessage(Stringable $text): void
    {
        Log::info(auth()->user());
        Log::info('handleChatMessage');
    }

    public function dismiss(): void
    {
        Log::info('dismiss');
    }

    public function start(): void
    {
//        Log::info('start');

//        $this->chat->message("There are already 10 of us 🎉")->send();
        $this->reply("🤘");
        $this->chat->action(ChatActions::TYPING)->send();

//        $this->reply("👋 Welcome to Rocker Bot!");

//        sleep(3);

//        $this->getCountries();
//        sleep(2);


//        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $buttons = [
            Button::make('web')->webApp(env('APP_URL').'/test'),
        ];

        $this->sendMessageWithButton('menu', $buttons, 4702);

//        $this->chat->message(trans('messages.indicate_city'))
//            ->keyboard(
//                Keyboard::make()->buttons([
//                    Button::make('➕ Add event')->webApp(env('APP_URL').'/profile/events/create'),
//                    Button::make('📃 Events list')->action('stats'),
//                    Button::make('📅 Events list (web)')->webApp(env('APP_URL')),
//                    Button::make('web')->webApp(env('APP_URL').'/test'),
//                ])
//            )->send();
    }

    protected function sendMessageWithButton(string $messageText, array $buttons, int|null $messageId = null): void
    {
        Log::info('$messageId keyboard', [$messageId]);
        if ($messageId) {
            $this->chat->replaceKeyboard(
                $messageId + 2,
                Keyboard::make()->buttons($buttons)
            )->send();
        } else {
            Log::info('Sending new message with keyboard');
            $this->chat->message($messageText)
                ->keyboard(
                    Keyboard::make()->buttons($buttons)
                )
                ->send();
        }
    }


    public function getCountries(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];

//        Log::info('getCountries', [$this]);

        foreach (trans('settings.countries') as $icon => $value) {
            $checked = auth()->user()->settings->country === $value ? ' ☑️' : '';
            array_unshift($buttons, Button::make($icon.$checked)->action('update_settings')->param('country', $value));
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_country'), $buttons, $lastMessageId);
    }


//    public function menu(): void
//    {
//        $buttons = [
//            Button::make('back')->action('settings'),
//        ];
//
//        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());
//
//
//        $this->sendMessageWithButton(trans('messages.indicate_country'), $buttons, $lastMessageId);
//    }

    private function prepareMessageParams(string $chatId, ?int $currentMessageId): ?int
    {
        $cacheKey = "chat:{$chatId}:message_id";
        $lastMessageId = Cache::store('redis')->get($cacheKey);


        Log::info(
            'Cached message_id',
            [
                'cacheKey' => $cacheKey,
                'lastMessageId' => $lastMessageId,
                'currentMessageId' => $currentMessageId,
            ]
        );


        if ($lastMessageId + 2 <= $currentMessageId) {
            Log::info('Using cached message_id');
            Cache::store('redis')->put($cacheKey, $currentMessageId, 432000);

            return $lastMessageId;
        }

        return null;
    }


    public function add_event_instruction(): void
    {
        $buttons = [
            Button::make(trans('menu.add_event'))->webApp(config('app.url').'/profile/events/create'),
        ];

        $this->chat
            ->photo('images/add_event_instruction.jpg')
            ->html(trans('messages.add_event_instruction'))
            ->keyboard(Keyboard::make()->buttons($buttons))
            ->send();
    }


    protected function handleChatMemberJoined(UserBot|User $member): void
    {
        Log::info('handleChatMemberJoined');
        $this->chat->html("Welcome")->send();
    }

    protected function handleMigrateToChat(): void
    {
        Log::info('handleMigrateToChat');
        $this->chat->html("Chat migrated")->send();
    }

    public function menu(): void
    {
        Log::info('menu');

        $buttons = [
            Button::make(trans('menu.add_event'))->webApp(config('app.url').'/profile/events/create'),
            Button::make(trans('menu.events_list'))->action('stats'),
            Button::make(trans('menu.events_list_web'))->webApp(config('app.url')),
            Button::make(trans('menu.favorite_events'))->action('stats'),
            Button::make(trans('menu.settings'))->action('settings'),
//            Button::make(rand(1, 100))->webApp(env('APP_URL').'/test'),
        ];

        if (auth()->user()->isAdmin()) {
            $buttons[] = Button::make(trans('menu.admin_panel'))->webApp(config('app.url').'/test');
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

//        Log::info('lastMessageId--1', [$lastMessageId]);
        $this->sendMessageWithButton(
            trans('menu.menu'),
            $buttons,
            $lastMessageId
        );
    }

    public function settings(): void
    {
        $buttons = [
            Button::make(trans('settings.country'))->action('stats'),
            Button::make(trans('settings.city'))->action('stats'),

//            Button::make(rand(1, 100))->webApp(env('APP_URL').'/test'),
        ];
        Log::info('asadsa', [$this]);
        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());
        $this->sendMessageWithButton(
            trans('settings.countries.Armenia 🇦🇲'),
            $buttons,
            $lastMessageId
        );
//        $this->reply("settings");
    }

}
