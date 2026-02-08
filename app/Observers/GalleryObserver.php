<?php

namespace App\Observers;

use App\Models\Gallery;
use App\Notifications\EntityCreatedNotification;

class GalleryObserver
{
    public function created(Gallery $gallery): void
    {
        $gallery->user->notify(new EntityCreatedNotification(
            'gallery',
            $gallery->title ?? 'Gallery',
            route('galleries.show', $gallery),
            'created'
        ));
    }
}
