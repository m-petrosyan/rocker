<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventRepository
{
    public static function eventsList($limit = 50, $events = null)
    {
        $params = [
            'limit' => $limit,
        ];

        $ids = $events?->pluck('event_id')->toArray();

        if ($ids) {
            $params['ids'] = $ids;
        }
        if (auth()->user()->role === 'admin') {
            dump($params);
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

        if (!empty($data['data'])) {
            $apiEventIds = collect($data['data'])->pluck('id')->toArray();
            $eventModels = Event::query()->whereIn('event_id', $apiEventIds)->get()->keyBy('event_id');

            $data['data'] = collect($data['data'])->map(function ($apiEvent) use ($eventModels) {
                $eventModel = $eventModels->get($apiEvent['id']);
                if ($eventModel) {
                    $apiEvent = array_merge($apiEvent, $eventModel->toArray());
                }

                return $apiEvent;
            })->toArray();
        }

        return $data;
    }

    public static function get($eventId)
    {
        $response = Http::get('https://bot.rocker.am/api/event/'.$eventId);

        if (!$response->ok()) {
            return null;
        }

        $json = $response->json();

        if (!is_array($json) || !isset($json['data'])) {
            return null;
        }

        return $json['data'];
    }


    public static function count(): int
    {
        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/events_count');
            $data = $response->json();
            Cache::put('events_count', $data, now()->addHour(2));
        } catch (\Exception $e) {
            if (Cache::has('events')) {
                $data = Cache::get('events_count');
            } else {
                $data = ['data' => [], 'error' => $e->getMessage()];
            }
        }

        return $data;
    }
}
