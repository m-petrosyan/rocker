<?php

namespace App\Repositories;

use App\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryReoisitory
{
    public static function userGallery($user)
    {
        return $user->galleries()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }


    public static function allGalleries(): LengthAwarePaginator
    {
        return Gallery::query()
            ->with(['user.roles'])
            ->orderByRaw('ISNULL(date), date ASC')
            ->paginate(20);
    }
}
