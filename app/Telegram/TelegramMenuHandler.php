<?php

namespace App\Telegram;

use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Log;

trait TelegramMenuHandler
{
    public function menu(): void
    {
        Log::info('menu', [$this->chat]);

        $buttons = [
            Button::make(trans('menu.add_event'))->webApp(config('app.url').'/profile/events/create'),
            Button::make(trans('menu.events_list'))->action('events_list'),
            Button::make(trans('menu.events_list_web'))->webApp(config('app.url')),
            Button::make(trans('menu.favorite_events'))->action('stats'),
            Button::make(trans('menu.settings'))->action('settings'),
        ];

        if (auth()->user()->isAdmin()) {
            $buttons[] = Button::make(trans('menu.admin_panel'))->webApp(config('app.url').'/test');
        }

        $lastMessageId = $this->prepareMessageParams($this->chat->chat_id, $this->message?->id());
        $this->sendMessageWithButton(trans('menu.menu'), $buttons, $lastMessageId);
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
}
