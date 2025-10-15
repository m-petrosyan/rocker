<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public static function usersList($count = 100): LengthAwarePaginator
    {
        return User::with('roles')->withCount(['bands', 'events', 'blogs', 'galleries'])
            ->orderBy('created_at', 'desc')
            ->paginate($count, ['id', 'name', 'email']);
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
