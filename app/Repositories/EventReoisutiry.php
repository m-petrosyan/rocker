<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventReoisutiry
{
    public static function eventsList($limit = 0)
    {
        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/event', [
                'limit' => $limit,
            ]);
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
