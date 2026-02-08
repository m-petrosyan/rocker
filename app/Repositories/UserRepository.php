<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public static function usersList($count = 100, $filter = null, $sort = 'newest'): LengthAwarePaginator
    {
        return User::with(['roles', 'settings'])
            ->withCount(['bands', 'events', 'blogs', 'galleries', 'chat'])
            ->when($filter === 'blocked', fn($query) => $query->whereHas('blockedRecord'))
            ->when($filter === 'bot', fn($query) => $query->whereHas('chat'))
            ->when($filter === 'web', fn($query) => $query->doesntHave('chat'))
            ->when($sort === 'active', function ($query) {
                $query->orderBy('last_activity', 'desc');
            }, function ($query) use ($sort) {
                $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');
            })
            ->paginate($count);
    }

    public static function count($bot = null): int
    {
        return User::query()
            ->when(
                $bot,
                fn($query) => $query->whereHas('chat'),
                fn($query) => $query->doesntHave('chat')
            )->count();
    }
}
