<?php

namespace App\Telegram;

use App\Events\EventNotification;
use App\Jobs\EventNotificationJob;
use App\Models\Event;
use App\Repositories\EventRepository;
use Carbon\Carbon;
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
                    $event->start_date_short.' / '.$event->title.'  '.$flag
                )
                    ->action('get_event')
                    ->param('eventId', $event->id);
            }

            auth()->user()->chat
                ->message('events')
                ->keyboard(Keyboard::make()->buttons($buttons))
                ->send();
        }
    }

    public function get_event(int $eventId): void
    {
        auth()->user()->chat->action(ChatActions::TYPING)->send();

        $event = Event::findOrFail($eventId);

        dispatch(new EventNotificationJob($event, auth()->user()->load('chat')));
    }

    public function favorite_events(): void
    {
        if (auth()->user()->favorites()->exists()) {
            $buttons = [];

            foreach (auth()->user()->favorites()->get() as $event) {
                $buttons[] = Button::make(Carbon::parse($event->start_date)->format('d.m').' / '.$event->title)
                    ->action('get_event')
                    ->param('eventId', $event->id)
                    ->width(0.5);
                $buttons[] = Button::make('Delete')
                    ->action('remove_from_favorite')
                    ->param('eventId', $event->id)
                    ->width(0.5);
            }

            auth()->user()->chat
                ->message('Favorite events')
                ->keyboard(Keyboard::make()->buttons($buttons))
                ->send();
        } else {
            auth()->user()->chat->message('Your list is empty')->send();
        }
    }

}
