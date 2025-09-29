<?php

namespace App\Telegram;

use App\Jobs\EventNotificationJob;
use App\Models\Event;
use App\Repositories\EventRepository;
use DefStudio\Telegraph\Enums\ChatActions;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;

trait TelegramEventsHandler
{

    public function events_list(): void
    {
        auth()->user()->chat->action(ChatActions::TYPING)->send();

        if (!auth()->user()->settings->country) {
            $this->chat->message(trans('messages.indicate_country'))->send();

            $this->get_countries();
        } else {
            $events = EventRepository::eventsList();

            $buttons = [];

            foreach ($events->items() as $event) {
                $flag = $event->country === 'am' ? '🇦🇲' : '🇬🇪';

                $buttons[] = Button::make(
                    $event->start_date.' / '.$event->title.'  '.$flag
                )
                    ->action('get_event')
                    ->param('id', $event->id);
            }

            auth()->user()->chat
                ->message('events')
                ->keyboard(Keyboard::make()->buttons($buttons))
                ->send();
        }
    }

    public function get_event(int $id): void
    {
        auth()->user()->chat->action(ChatActions::TYPING)->send();

        $event = Event::findOrFail($id);

        dispatch(new EventNotificationJob($event));
    }

}
