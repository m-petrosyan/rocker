<?php

namespace App\Repositories;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public static function eventsList($limit = 50, $page = 1, $past = false): LengthAwarePaginator
    {
        $country = auth()?->user()->settings->country ?? 'am';

        return Event::query()
            ->whereRelation('status', 'status', EventStatusEnum::ACCEPTED->value)
            ->where(function ($query) use ($country) {
                $query->whereIn('country', $country === 'all' ? ['am', 'ge'] : [$country]);
            })
            ->with(['status'])
            ->when(!$past, fn($query) => $query->where('start_date', '>=', now()))
            ->when($past, fn($query) => $query->where('start_date', '<', now()))
            ->orderBy('start_date', $past ? 'desc' : 'asc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public static function requestList($limit = 50, $page = 1): LengthAwarePaginator
    {
        return Event::query()->whereRelation('status', 'status', EventStatusEnum::PENDING->value)
            ->with('user', 'status')
            ->orderBy('created_at')
            ->paginate($limit, ['*'], 'page', $page);
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
            ->when(fn($query) => $query->where('start_date', '>=', now()))
            ->whereRelation('status', 'status', '!=', EventStatusEnum::DELETED->value)
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
