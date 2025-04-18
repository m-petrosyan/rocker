<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventReoisutiry
{
    public static function eventsList($limit = 0, $events = null)
    {
        $params = [
            'limit' => $limit,
        ];

        $ids = $events?->pluck('event_id')->toArray();
        if ($ids) {
            $params['ids'] = $ids;
        }

        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
            $data = $response->json();
            Cache::put('events', $data, now()->addHour(2));
        } catch (\Exception $e) {
            if (Cache::has('events')) {
                $data = Cache::get('events');
            } else {
                $data = ['data' => [], 'error' => $e->getMessage()];
            }
        }

        return $data;
    }
}
