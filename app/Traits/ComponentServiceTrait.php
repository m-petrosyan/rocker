<?php

namespace App\Traits;

use App\Models\Band;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

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


    public function addImage(Model $model, object $image, string $name): void
    {
        if ($image instanceof UploadedFile && $image->isValid()) {
            $model->addMedia($image)
                ->toMediaCollection($name);
        }
    }

    public function addImages(Model $model, array|null $images, $name = 'images'): void
    {
        if (isset($images)) {
            foreach ($images as $image) {
                $this->addImage($model, $image, $name);
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

    public function setCover(Model $model, int|null $cover): void
    {
        if (isset($cover) && ($cover !== $model->cover)) {
            $model->update(['cover' => $model->images_url[$cover]['id']]);
        }
    }

    public function updateLinks(Model $model, $links = []): void
    {
        if (isset($links)) {
            $model->links()->delete();
            foreach ($links as $link) {
                $model->links()->create(
                    ['url' => $link['url']],
                );
            }
        }
    }
}
