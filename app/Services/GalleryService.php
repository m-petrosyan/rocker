<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $venueId = $this->addLocation($attributes);

        $gallery = auth()->user()->galleries()->create(array_merge(array_filter($attributes), ['venue_id' => $venueId])
        );

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function update($gallery, $attributes): void
    {
        $venueId = $this->addLocation($attributes);

        $gallery->update(array_merge(array_filter($attributes), ['venue_id' => $venueId]));

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function destroy($gallery): void
    {
        $gallery->clearMediaCollection('images');

        $gallery->delete();
    }
}
