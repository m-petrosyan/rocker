<?php

namespace App\Repositories;

class GalleryReoisitory
{
    public static function userGallery()
    {
        return auth()->user()->galleries()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
