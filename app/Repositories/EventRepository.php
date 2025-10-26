<?php

namespace App\Repositories;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;

class EventRepository
{
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

    public static function userEvents(object $user): LengthAwarePaginator
    {
        return $user?->events()
            ->with('status')
            ->when(fn($query) => $query->where('start_date', '>=', now()))
            ->whereRelation('status', 'status', '!=', EventStatusEnum::DELETED->value)
            ->orderBy('start_date')
            ->paginate();
    }

    public static function count($status = EventStatusEnum::ACCEPTED->value): mixed
    {
        return Event::query()->whereRelation('status', 'status', '=', $status)
            ->count();
    }
}
