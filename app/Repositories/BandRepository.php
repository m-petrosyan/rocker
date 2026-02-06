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


    public static function bandList(int $limit = 50, string $sort = ''): LengthAwarePaginator
    {
        return Band::query()
            ->whereNotNull('user_id')
            ->whereNotNull('info')
            ->when(
                $sort,
                fn($query) => $query->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc'),
                fn($query) => $query->inRandomOrder()
            )
            ->paginate($limit, ['id', 'name', 'slug']);
    }

    public static function bandNamesList(bool $createdFirst = true): array
    {
        return Band::query()
            ->when($createdFirst, fn($query) => $query->orderByDesc('user_id'))
            ->get(['id', 'name'])
            ->toArray();
    }

    public static function getBand(Band $band, array $loads = []): Band
    {
        return $band->load(
            'genres',
            'galleries:id,title,user_id,date,cover',
            'galleries.user',
            'galleries.media',
            'links',
            'albums',
            'events',
            'blogs',
            ...$loads
        );
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
