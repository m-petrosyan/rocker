<?php

namespace App\Repositories;

use App\Enums\EventStatusEnum;
use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class EventRepository
{
    public static function eventsList($limit = 50, $page = 1, $past = false): LengthAwarePaginator
    {
        if (app()->bound('sitemap_mode') && app('sitemap_mode') === true) {
            $country = 'all';
        } else {
            $country = auth()?->user()->settings->country ?? 'am';
        }

        Log::info($country);

        return Event::query()
            ->whereRelation('status', 'status', EventStatusEnum::ACCEPTED->value)
            ->where(function ($query) use ($country) {
                $query->whereIn('country', $country === 'all' ? ['am', 'ge'] : [$country]);
            })
            ->with(['status'])
            ->when(!$past, fn($query) => $query->whereDate('start_date', '>=', today()))
            ->when($past, fn($query) => $query->whereDate('start_date', '<', today()))
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

    public static function userEvents(object $user, $count = 20): LengthAwarePaginator
    {
        return $user?->events()
            ->with('status')
            ->withCount(['views'])
            ->whereDate('start_date', '>=', today('Asia/Yerevan'))
            ->whereRelation('status', 'status', '!=', EventStatusEnum::DELETED->value)
            ->orderBy('start_date')
            ->paginate($count);
    }

    public static function count($status = EventStatusEnum::ACCEPTED->value): int
    {
        return Event::query()->whereRelation('status', 'status', '=', $status)
            ->count();
    }
}
