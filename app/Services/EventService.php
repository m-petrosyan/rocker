<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventService
{
    public function index(): array
    {
        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/event');
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
