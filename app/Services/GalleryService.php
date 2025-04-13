<?php

namespace App\Services;

use App\Models\Band;

class GalleryService
{
    public function store(array $attributes): void
    {
        if (isset($attributes['bands'])) {
            foreach ($attributes['bands'] as $band) {
                Band::query()->firstOrCreate(['name' => $band['name']], $band);
            }
        }
    }
}
