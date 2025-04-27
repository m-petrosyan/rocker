<?php

namespace App\Repositories;

use App\Models\Band;

class BandRepository
{
    public static function userBands($user)
    {
        return $user->bands()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }


    public static function bandList(): array
    {
        return Band::query()->get(['id', 'name'])->toArray();
    }
}
