<?php

namespace App\Services;

use App\Models\Venue;
use App\Traits\ComponentServiceTrait;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $venueId = null;
        if (isset($attributes['cid'])) {
            $venue = Venue::firstOrCreate(
                ['cid' => $attributes['cid']],
                [
                    'cid' => $attributes['cid'],
                    'location' => $attributes['location'],
                    'cordinates' => $attributes['cordinates'],
                ]
            );
            $venueId = $venue->id;
        }

        $gallery = auth()->user()->galleries()->create(array_merge($attributes, ['venue_id' => $venueId]));

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function update($gallery, $attributes): void
    {
//        phpinfo();
        $gallery->update($attributes);

        $this->addImages($gallery, $attributes['images']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function destroy($gallery): void
    {
        $gallery->clearMediaCollection('images');

        $gallery->delete();
    }
}
