<?php

namespace App\Telegram;

use App\Models\UserBot;
use App\Repositories\EventRepository;
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

        $this->chat->message(trans('messages.indicate_city'))
            ->keyboard(
                Keyboard::make()->buttons([
                    Button::make('➕ Add event')->webApp(env('APP_URL').'/profile/events/create'),
                    Button::make('📃 Events list')->action('stats'),
                    Button::make('📅 Events list (web)')->webApp(env('APP_URL')),
                    Button::make('web')->webApp(env('APP_URL').'/test'),
                ])
            )->send();
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
        Log::info('menu', [$this->chat]);

        $buttons = [
            Button::make(trans('menu.add_event'))->webApp(config('app.url').'/profile/events/create'),
            Button::make(trans('menu.events_list'))->action('stats'),
            Button::make(trans('menu.events_list_web'))->webApp(config('app.url')),
            Button::make(trans('menu.favorite_events'))->action('stats'),
            Button::make(trans('menu.settings'))->action('settings'),
            Button::make('test')->webApp(config('app.url').'/test'),
        ];

        if (auth()->user()->isAdmin()) {
            $buttons[] = Button::make(trans('menu.admin_panel'))->webApp(config('app.url').'/test');
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());
        $this->sendMessageWithButton(trans('menu.menu'), $buttons, $lastMessageId);
    }


/// Settings
    public function settings(): void
    {
        $buttons = [
            Button::make(trans('settings.country'))->action('get_countries'),
            Button::make(trans('settings.city'))->action('get_cities'),
            Button::make('back')->action('menu'),
        ];

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(
            trans('menu.menu'),
            $buttons,
            $lastMessageId
        );
    }


    public function get_countries(): void
    {
        $buttons = [
            Button::make('back')->action('settings'),
        ];

        foreach (trans('settings.countries') as $icon => $value) {
            $checked = auth()->user()->settings->country === $value ? ' ☑️' : '';

            array_unshift(
                $buttons,
                Button::make($icon.$checked)->action('update_settings')
                    ->param('key', 'country')->param('value', $value)
            );
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_country'), $buttons, $lastMessageId);
    }


    public function get_cities($country = null): void
    {
        $country = $country ?? auth()->user()->settings->country;

//        if (!$country) {
//            $this->get_countries();
//        }

        $buttons = [
            Button::make('back')->action('settings'),
        ];

        foreach (trans("settings.cities.$country") as $key => $value) {
            $checked = auth()->user()->settings->city === $value ? ' ☑️' : '';

            array_unshift(
                $buttons,
                Button::make($key.$checked)->action('update_settings')
                    ->param('key', 'city')->param('value', $value)
            );
        }
        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());

        $this->sendMessageWithButton(trans('messages.indicate_city'), $buttons, $lastMessageId);
    }

    public function update_settings($key, $value): void
    {
        auth()->user()->settings()->update([$key => $value]);

        if ($key === 'country') {
            if ($value === 'all') {
                $this->settings();
                $this->reply("Saved");
            } else {
                $this->get_cities($value);
            }
        } elseif ($key === 'city') {
            $this->reply("Saved");
            $this->menu();
            sleep(2);
            $this->chat->message(trans('messages.change_city'))->send();
        }
    }

    public function events_list()
    {
        $this->chat->action(ChatActions::TYPING)->send();

        if (!auth()->user()->settings->country) {
            $this->chat->message(trans('messages.indicate_country'))->send();

            $this->get_countries();

            return false;
        } else {
            $this->get_events_list();
        }
    }

    public function get_events_list(): void
    {
        $events = EventRepository::activeEvents();

        $buttons = [];

        foreach ($events as $event) {
            $flag = $event->country === 'am' ? '🇦🇲' : '🇬🇪';

            $buttons[] = Button::make(
                $event->start_date.' / '.$event->title.'  '.$flag
            )
                ->action('get_event')
                ->param('id', $event->id);
        }

        $this->get_chat()
            ->message('events')
            ->keyboard(Keyboard::make()->buttons($buttons))
            ->send();
    }
    // events

}
