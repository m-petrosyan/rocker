<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $gallery = auth()->user()->galleries()->create($attributes);

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes['bands']);
    }

    public function update($gallery, $attributes): void
    {
//        phpinfo();
        $gallery->update($attributes);

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes['bands']);
    }
}
