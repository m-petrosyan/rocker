<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Facades\Http;

class EventRepository
{
    public static function userEvents()
    {
        return auth()->user()->events()
//            ->whereHas('confirm', function ($query) {
//            $query->where('confirmed', true);
//        })
            ->orderBy('start_date')->paginate();
    }

    public static function eventsList($limit = 51, $page = 1, $past = false)
    {
        $params = [
            'limit' => $limit,
            'page' => $page,
            'past' => $past,
        ];

        /// add paramas
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
        return Event::count();
    }
}
