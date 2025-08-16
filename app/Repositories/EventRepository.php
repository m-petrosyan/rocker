<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventRepository
{

    public static function activeEvents(string|null $country = null)
    {
        return Event::where(['still_relevant' => true])
            ->when(!$country, function ($query, $country) {
                if (auth()->user()->settings->country === 'all') {
                    $query->whereIn('country', ['am', 'ge']);
                } else {
                    $query->where('country', auth()->user()->settings->country);
                }
            }, function ($query) use ($country) {
                if ($country === 'all') {
                    $query->whereIn('country', ['am', 'ge']);
                } else {
                    $query->where('country', $country);
                }
            })
            ->with('user')
            ->whereHas('confirm', function ($query) {
                $query->where('confirmed', true);
            })
            ->orderBy('start_date');
    }

    public static function userEvents()
    {
        return auth()->user()->events()
//            ->whereHas('confirm', function ($query) {
//            $query->where('confirmed', true);
//        })
            ->orderBy('start_date')->paginate();
    }

    public static function eventsList($limit = 50)
    {
        return Event::where('start_date', '>=', now())
            ->with('user')
//            ->whereHas('confirm', function ($query) {
//                $query->where('confirmed', true);
//            })
            ->orderBy('start_date')
            ->paginate($limit);
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


//        $event = Event::query()->where('event_id', $eventId)->first()?->load('bands', 'views');
////        dump(1);
//        // Если локальное событие найдено, объединяем данные и фиксируем просмотр
//        if ($event) {
//            dd(1);
//            $json['data'] = array_merge($json['data'], $event->toArray());
//            views($event)->record();
//        }
//        dd($json['data']);

        // Возвращаем объединённые данные
        return $json['data'];
    }


    public static function count(): mixed
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
