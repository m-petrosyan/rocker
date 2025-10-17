<?php

namespace App\Jobs;

use App\Models\Event;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EventCahnnelNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function handle(): void
    {
        $event = $this->event;

        $keyboard = Keyboard::make();
        $buttons = [];

        if (!empty($event->link)) {
            $buttons[] = Button::make('Event link')->url($event->link);
        }

        if (!empty($event->ticket)) {
            $buttons[] = Button::make('Tickets')->url($event->ticket);
        }

        if (!empty($buttons)) {
            $keyboard->buttons($buttons);
        }

        $city = $this->set_city($event);
        $channel = null;
        $thread_id = null;

        if ($event->country === 'am') {
            if ($city === 'gyumri') {
                $channel = '-1001935369399';
            } else {
                $channel = '-1001583147579';
                $thread_id = 97732;
            }
        } else {
            if ($city === 'batumi') {
                $channel = '-1001855943000';
            } else {
                $channel = '-1001681397226';
                $thread_id = 1139;
            }
        }
        $content = $this->getEventContent($event);

        try {
            $telegraph = Telegraph::bot(config('telegraph.configs.token'))
                ->chat($channel)
                ->photo($event->poster)
                ->html($content)
                ->keyboard($keyboard);


            if ($thread_id !== null) {
                $telegraph->withData('message_thread_id', $thread_id);
            }

            $telegraph->send();
        } catch (\Throwable $e) {
            Log::error('Telegram send error', [
                'event_id' => $event->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function set_city(Event $event): string
    {
        return strtolower($event->city ?? 'yerevan');
    }
}
