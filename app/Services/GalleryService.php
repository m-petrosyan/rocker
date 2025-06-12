<?php

namespace App\Services;

use App\Notifications\NewCreationNotification;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class GalleryService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $venueId = $this->addLocation($attributes);

        $gallery = auth()->user()->galleries()->create(
            array_merge(Arr::except($attributes, 'cover'), ['venue_id' => $venueId])
        );

        $this->addImages($gallery, $attributes['images']);

        $this->setCover($gallery, $attributes['cover']);

        $this->addSyncBand($gallery, $attributes);

        Notification::route('mail', config('mail.to.address'))
            ->notify(new NewCreationNotification($gallery));
    }

    public function update($gallery, $attributes): void
    {
        $venueId = $this->addLocation($attributes);

        $gallery->update(array_merge(Arr::except($attributes, 'cover'), ['venue_id' => $venueId]));

        $this->addImages($gallery, $attributes['images']);

        $this->setCover($gallery, $attributes['cover']);

        $this->addSyncBand($gallery, $attributes);
    }

    public function destroy($gallery): void
    {
        $gallery->clearMediaCollection('images');

        $gallery->delete();
    }
}
