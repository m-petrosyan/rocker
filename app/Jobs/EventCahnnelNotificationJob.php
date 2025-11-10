<?php

namespace App\Jobs;

use App\Models\Event;
use App\Traits\EventFormatingTrait;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EventCahnnelNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, EventFormatingTrait;

    public Event $event;
    public string $mode;

    public function __construct(protected int $eventId, string $mode = 'real')
    {
        $this->mode = $mode;
    }

    public function handle(): void
    {
        $event = Event::findOrFail($this->eventId)->load('media');

        $keyboard = Keyboard::make();
        $buttons = [];


        $city = $this->set_city($event);
        $channel = null;
        $thread_id = null;

        if ($this->mode === 'testing') {
            $channel = '-1002493394276';
        } else {
            if ($event->country === 'am') {
                if ($city === 'gyumri') {
                    $channel = '-1001935369399';
                } else {
                    $channel = '-1001583147579';
                    $thread_id = 97732;
                }
                $buttons[] = Button::make('Rocker link')->url(route('events.show', $event->id));
            } else {
                if ($city === 'batumi') {
                    $channel = '-1001855943000';
                } else {
                    $channel = '-1001681397226';
                    $thread_id = 1139;
                }
            }
        }

        if (!empty($event->link)) {
            $buttons[] = Button::make('Event link')->url($event->link);
        }

        if (!empty($event->ticket)) {
            $buttons[] = Button::make('Tickets')->url($event->ticket);
        }

        if (!empty($buttons)) {
            $keyboard->buttons($buttons);
        }

        $content = $this->getEventContent($event);

        $telegraph = Telegraph::bot(config('telegraph.configs.token'));

        if ($thread_id !== null) {
            $telegraph = $telegraph
                ->chat($channel)
                ->withData('message_thread_id', $thread_id);
        } else {
            $telegraph = $telegraph
                ->chat($channel);
        }

        $telegraph
            ->photo($event->poster['thumb'])
            ->html($content)
            ->keyboard($keyboard)
            ->send();
    }

    private function set_city(Event $event): string
    {
        $default = $event->country === 'am' ? 'yerevan' : 'tbilisi';

        return strtolower($event->city ?? $default);
    }
}