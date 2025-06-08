<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Support\Facades\Http;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function retrieved(Event $event): void
    {
        dump(1);
        $response = Http::get('https://bot.rocker.am/api/event/'.$event['event_id']);;
        $apiEvent = $response->json()['data'] ?? null;

        if ($apiEvent && isset($apiEvent['id']) && $apiEvent['id'] == $event['event_id']) {
//            dd($apiEvent);
            foreach ($apiEvent as $key => $value) {
                if ($key !== 'id') {
                    $event->setAttribute($key, $value);
                }
            }
        }
    }

}
