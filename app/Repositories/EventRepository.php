<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventRepository
{
    public static function userEvents($events = null)
    {
        $ids = $events?->pluck('event_id')->toArray();

        $params['ids'] = $ids;

        $data = self::request($params);


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

    public static function eventsList($limit = 50)
    {
        $params = [
            'limit' => $limit,
        ];

        return self::request($params, true);
    }


    public static function request($params = [], $cache = false)
    {
        try {
            $response = Http::throw()->get('https://bot.rocker.am/api/event', $params);
            $data = $response->json();
            if ($cache) {
                Cache::put('events', $data, now()->addHour(2));
            }
        } catch (\Exception $e) {
            if (Cache::has('events') && $cache) {
                $data = Cache::get('events');
            } else {
                $data = ['data' => [], 'error' => $e->getMessage()];
            }
        }

        return $data;
    }


    public static function get($eventId)
    {
        // Выполняем запрос к API
        $response = Http::get('https://bot.rocker.am/api/event/'.$eventId);

        // Проверяем, успешен ли запрос
        if (!$response->ok()) {
            return null;
        }

        // Получаем JSON-ответ
        $json = $response->json();


//        // Проверяем, является ли ответ массивом и содержит ли ключ 'data'
//        if (!is_array($json) || !isset($json['data'])) {
//            return null;
//        }


        $event = Event::query()->where('event_id', $eventId)->first()->load('bands', 'views');
        dump(1);
        // Если локальное событие найдено, объединяем данные и фиксируем просмотр
        if ($event) {
            $json['data'] = array_merge($json['data'], $event->toArray());
            views($event)->record();
        }

        // Возвращаем объединённые данные
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
