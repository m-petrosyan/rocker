<?php

namespace App\Repositories;

use App\Models\Band;

class BandRepository
{
    public static function bandList(): array
    {
        return Band::query()->get(['id', 'name'])->toArray();
    }
}
