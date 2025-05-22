<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public static function usersList(): LengthAwarePaginator
    {
        return User::with('roles')->withCount(['bands', 'events', 'blogs', 'galleries'])
            ->orderBy('created_at', 'desc')
            ->paginate(100, ['id', 'name', 'email']);
    }

    public static function count(): int
    {
        return User::query()->count();
    }
}
