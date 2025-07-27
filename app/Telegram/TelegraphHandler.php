<?php

namespace App\Telegram;

use App\Models\UserBot;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

class TelegraphHandler extends WebhookHandler
{
//    public function __construct()
//    {
//        Log::info('TelegraphHandler initialized');
//        parent::__construct();
////        Log::info($this->chat);
////        auth()->loginUsingId($this->chat);
//    }

    public function handleChatMessage(Stringable $text): void
    {
        Log::info(auth()->user());
        Log::info('handleChatMessage');
    }

    public function dismiss()
    {
        Log::info('dismiss');
    }

    public function start(): void
    {
        Log::info('start');

        $this->reply("👋 Welcome to Rocker Bot!");
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

//        Log::info($this->chat->user->isAdmin());
        Log::info(auth()->user()->isAdmin());
        $this->chat->message(trans('messages.indicate_city'))
            ->keyboard(
                Keyboard::make()->buttons([
                    Button::make('➕ Add event')->webApp(env('APP_URL').'/profile/events/create'),
                    Button::make('📃 Events list')->action('stats'),
                    Button::make('📅 Events list (web)')->webApp(env('APP_URL')),
                    Button::make('web')->webApp(env('APP_URL').'/test'),
                ])
            )->send();


//        'menu_title' => '🎉 Menu for now:',
//    'menu' => '📃 Menu',
//    'events_list' => '📅 Events list',
//    'events_list_web' => '📅 Events list (web)',
//    'add_event' => '➕ Add event',
    }
}
