<?php

namespace App\Services;

use App\Models\Band;

class GalleryService
{
    public function store(array $attributes): void
    {
        $gallery = auth()->user()->galleries()->create($attributes);

        foreach ($attributes['images'] as $image) {
            $gallery->addMedia($image)
                ->toMediaCollection('images');
        }

        if (isset($attributes['bands'])) {
            foreach ($attributes['bands'] as $band) {
                Band::query()->firstOrCreate(['name' => $band['name']], $band);
            }
        }
    }
}
