<?php

namespace App\Traits;

use App\Models\Band;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;

trait ComponentServiceTrait
{
    public function addSyncBand(Model $model, array|null $bands): void
    {
        if (isset($bands['bands'])) {
            $bandIds = [];
            foreach ($bands['bands'] as $bandData) {
                $band = Band::query()->firstOrCreate(
                    ['name' => $bandData['name']],
                    $bandData + ['user_id' => auth()->user()->id]
                );
                $bandIds[] = $band['id'];
            }
            $model->bands()->sync($bandIds);
        }
    }

    public function addImages(Model $model, array|null $images): void
    {
        if (isset($images)) {
            foreach ($images as $image) {
                $model->addMedia($image)
                    ->toMediaCollection('images');
            }
        }
    }

    public function addLocation(array $attributes): ?int
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

        return $venueId;
    }
}
