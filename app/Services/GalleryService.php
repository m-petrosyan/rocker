<?php

namespace App\Services;

use App\Models\Venue;
use App\Traits\ComponentServiceTrait;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        if ($attributes['cid']) {
            Venue::query()->create([
                'cid' => $attributes['cid'],
                'title' => $attributes['location'],
                'address' => $attributes['location'],
                'cordinates' => $attributes['cordinates'],
            ]);
        }

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

    public function destroy($gallery): void
    {
        $gallery->clearMediaCollection('images');

        $gallery->delete();
    }
}
