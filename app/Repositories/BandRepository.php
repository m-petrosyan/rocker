<?php

namespace App\Repositories;

use App\Models\Band;
use Illuminate\Pagination\LengthAwarePaginator;

class BandRepository
{
    public static function userBands($user)
    {
        return $user->bands()
            ->withCount(['views'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }


    public static function bandList($limit = 50, $sort = 'newest'): LengthAwarePaginator
    {
        return Band::query()
            ->whereNotNull('user_id')
            ->whereNotNull('info')
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate($limit, ['id', 'name', 'slug']);
    }

    public static function bandNamesList(bool $createdFirst = true): array
    {
        return Band::query()
            ->when($createdFirst, fn($query) => $query->orderByDesc('user_id'))
            ->get(['id', 'name'])
            ->toArray();
    }

    public static function withoutPage(): array
    {
        return Band::query()->whereNull('user_id')->get(['id', 'name'])->toArray();
    }

    public static function count(): int
    {
        return Band::query()->whereNotNull('user_id')->count();
    }
}
