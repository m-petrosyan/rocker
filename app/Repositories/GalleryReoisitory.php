<?php

namespace App\Repositories;

use App\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryReoisitory
{
    public static function getGallery(Gallery $gallery): Gallery
    {
        return $gallery->load(['bands', 'user.roles', 'venue']);
    }


    public static function userGallery($user)
    {
        return $user->galleries()
            ->orderBy('created_at', 'desc', 'views', 'allViews', 'total_mb')
            ->paginate(30);
    }


    public static function galleryList($limit = 50): LengthAwarePaginator
    {
        return Gallery::query()
            ->with(['user.roles'])
            ->orderByRaw('ISNULL(date), date DESC, created_at DESC')
            ->paginate($limit);
    }

    public static function count(): int
    {
        return Gallery::query()->count();
    }
}
