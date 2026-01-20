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


    public static function galleryList($limit = 50, $sort = 'newest'): LengthAwarePaginator
    {
        return Gallery::query()
            ->with(['user'])
            ->orderBy('date', $sort === 'oldest' ? 'asc' : 'desc')
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate($limit);
    }

    public static function count(): int
    {
        return Gallery::query()->count();
    }
}
