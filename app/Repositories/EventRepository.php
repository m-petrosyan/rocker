<?php

namespace App\Repositories;

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
        return Event::where('start_date', '>=', now())
            ->with('user')
//            ->whereHas('confirm', function ($query) {
//                $query->where('confirmed', true);
//            })
            ->orderBy('start_date')
            ->paginate($limit);
    }

    public static function userEvents()
    {
        return auth()->user()->events()
//            ->whereHas('confirm', function ($query) {
//            $query->where('confirmed', true);
//        })
            ->orderBy('start_date')->paginate();
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
