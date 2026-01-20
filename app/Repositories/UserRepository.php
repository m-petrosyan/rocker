<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public static function usersList($count = 100, $filter = null, $sort = 'newest'): LengthAwarePaginator
    {
        return User::with('roles')
            ->withCount(['bands', 'events', 'blogs', 'galleries', 'chat'])
            ->when($filter === 'blocked', fn($query) => $query->whereHas('blockedRecord'))
            ->when($filter === 'bot', fn($query) => $query->whereHas('chat'))
            ->when($filter === 'web', fn($query) => $query->doesntHave('chat'))
            ->orderByRaw('EXISTS(SELECT 1 FROM user_bots WHERE user_bots.user_id = users.id)')
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
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
