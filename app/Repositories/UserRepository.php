<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public static function usersList($count = 100): LengthAwarePaginator
    {
        return User::with('roles')->withCount(['bands', 'events', 'blogs', 'galleries'])
            ->orderBy('created_at', 'desc')
            ->paginate($count, ['id', 'name', 'email']);


//        return User::query()
//            ->withCount([
//                'events' => function ($query) {
//                    $query->whereHas('confirm', function ($query) {
//                        $query->where('confirmed', true);
//                    });
//                },
//            ])
//            ->with('settings', 'role')
//            ->orderBy('events_count', 'desc')
//            ->orderBy(Role::query()->select('name')->whereColumn('roles.id', 'users.role_id'))
//            ->orderBy('id')
//            ->paginate(1500);
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
