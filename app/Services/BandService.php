<?php

namespace App\Services;

use App\Models\Band;
use App\Models\Genre;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Arr;

class BandService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        if (isset($attributes['name']['id'])) {
            $band = Band::query()->find($attributes['name']['id']);
            if (!$band->user_id) {
                $band->update(
                    ['name' => $attributes['name']['name'], 'user_id' => auth()->id()] +
                    $attributes
                );
            } else {
                abort('403', 'You are not allowed to edit this band');
            }
        } else {
            $band = auth()->user()->bands()->create(
                array_merge(['name' => $attributes['name']['name']], Arr::only($attributes, ['info']))
            );
        }

        if (isset($attributes['genres'])) {
            $genreIds = collect($attributes['genres'] ?? [])
                ->map(function ($genreData) {
                    $genre = Genre::query()->firstOrCreate(
                        ['name' => $genreData['name']]
                    );

                    return $genre->id;
                });

            $band->genres()->sync($genreIds);
        }

        $this->updateLinks($band, $attributes['links']);

        $this->addImage($band, $attributes['cover_file'], 'cover');
        if (isset($attributes['logo_file'])) {
            $this->addImage($band, $attributes['logo_file'], 'logo');
        }
    }

    public function update(Band $band, array $attributes): void
    {
        $band->update($attributes);

        if (isset($attributes['cover_file'])) {
            $band->clearMediaCollection('cover');
            $this->addImage($band, $attributes['cover_file'], 'cover');
        }
        if (isset($attributes['logo_file'])) {
            $band->clearMediaCollection('logo');
            $this->addImage($band, $attributes['logo_file'], 'logo');
        }

        if (isset($attributes['genres'])) {
            $genreIds = collect($attributes['genres'] ?? [])
                ->map(function ($genreData) {
                    $genre = Genre::query()->firstOrCreate(
                        ['name' => $genreData['name']]
                    );

                    return $genre->id;
                });

            $band->genres()->sync($genreIds);
        }

        $this->updateLinks($band, $attributes['links']);
    }

    public function destroy(Band $band): void
    {
        $band->update(['user_id' => null, 'info' => null]);
        $band->clearMediaCollection('cover');
        $band->clearMediaCollection('logo');
    }
}
