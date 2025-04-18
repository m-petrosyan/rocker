<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Arr;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $venueId = $this->addLocation($attributes);

        $gallery = auth()->user()->galleries()->create(
            array_merge($attributes, ['venue_id' => $venueId], Arr::only($attributes, ['cover']))
        );

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function update($gallery, $attributes): void
    {
        $venueId = $this->addLocation($attributes);


        $gallery->update(array_merge($attributes, ['venue_id' => $venueId], Arr::only($attributes, ['cover'])));

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function destroy($gallery): void
    {
        $gallery->clearMediaCollection('images');

        $gallery->delete();
    }
}
