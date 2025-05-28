<?php

namespace App\Observers;

use App\Models\Event;
use App\Repositories\EventRepository;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function retrieved(Event $event): void
    {
        dd($event);
        $apiEvent = EventRepository::get($event['event_id']);

        if ($apiEvent && isset($apiEvent['id']) && $apiEvent['id'] == $event['event_id']) {
            foreach ($apiEvent as $key => $value) {
                if ($key !== 'id') {
                    $event->setAttribute($key, $value);
                }
            }
        }
    }

}
