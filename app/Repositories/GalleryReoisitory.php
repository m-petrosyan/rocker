<?php

namespace App\Repositories;

use App\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryReoisitory
{
    public static function getGallery(Gallery $gallery): Gallery
    {
        $gallery->load(['bands', 'user.roles', 'venue']);

        $gallery->images_url = $gallery->imagesUrl();

        return $gallery;
    }


    public static function userGallery($user, array $appends = [])
    {
        return $user->galleries()
            ->withCount(['views'])
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }


    public static function galleryList($limit = 50): LengthAwarePaginator
    {
        return Gallery::query()
            ->with(['user'])
            ->orderByRaw('ISNULL(date), date DESC, created_at DESC')
            ->paginate($limit);
    }

    public static function count(): int
    {
        return Gallery::query()->count();
    }
}
