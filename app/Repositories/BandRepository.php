<?php

namespace App\Repositories;

use App\Models\Band;
use Illuminate\Pagination\LengthAwarePaginator;

class BandRepository
{
    public static function userBands($user)
    {
        return $user->bands()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }


    public static function bandList($limit = 50): LengthAwarePaginator
    {
        return Band::query()
            ->whereNotNull('user_id')
            ->whereNotNull('info')
            ->inRandomOrder()
            ->paginate($limit, ['id', 'name', 'slug']);
    }

    public static function bandNamesList(): array
    {
        return Band::query()->get(['id', 'name'])->toArray();
    }

    public static function withoutPage(): array
    {
        return Band::query()->whereNull('user_id')->get(['id', 'name'])->toArray();
    }
}
