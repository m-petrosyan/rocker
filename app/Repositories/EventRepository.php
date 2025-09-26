<?php

namespace App\Repositories;

use App\Enums\EventStatusEnum;
use App\Models\Event;

class EventRepository
{

//    public static function eventsList($limit = 51, $page = 1, $past = false, $country = 'am')
//    {
//        $params = [
//            'limit' => $limit,
//            'page' => $page,
//            'past' => $past,
//        ];
//
//
//        return Event::where(['still_relevant' => true])
//            ->when(!$country, function ($query, $country) {
//                if (auth()->user()->settings->country === 'all') {
//                    $query->whereIn('country', ['am', 'ge']);
//                } else {
//                    $query->where('country', auth()->user()->settings->country);
//                }
//            }, function ($query) use ($country) {
//                if ($country === 'all') {
//                    $query->whereIn('country', ['am', 'ge']);
//                } else {
//                    $query->where('country', $country);
//                }
//            })
//            ->with('user')
//            ->whereHas('confirm', function ($query) {
//                $query->where('confirmed', true);
//            })
//            ->orderBy('start_date');
//    }

    public static function eventsList($limit = 50)
    {
        return Event::query()
            ->with(['user.roles'])
            ->where('start_date', '>=', now())
            ->whereHas('confirm', fn($query) => $query->where('status', EventStatusEnum::ACCEPTED->value)
            )
            ->orderBy('start_date')
            ->paginate($limit);
    }

    public static function userEvents()
    {
//        dd(1);return $user ? $user->events()
//    ->with('status')
//    ->orderBy('start_date')
//    ->append('status_name')
//    ->paginate()
//    : collect();

        return auth()->user()?->events()
            ->with('status')
            ->whereHas('status', function ($query) {
                $query->where('status', '!=', EventStatusEnum::DELETED->value);
            })
            ->orderBy('start_date')
            ->paginate();
    }

//    public static function eventsList($limit = 50)
//    {
//        return Event::where('start_date', '>=', now())
//            ->with('user')
////            ->whereHas('confirm', function ($query) {
////                $query->where('confirmed', true);
////            })
//            ->orderBy('start_date')
//            ->paginate($limit);
//    }


    public static function count(): mixed
    {
        return Event::count();
    }
}
