<?php

namespace App\Services;

use App\Models\Band;
use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Arr;

class BandService
{
    use ComponentServiceTrait;

    public function store(array $attributes)
    {
        if (isset($attributes['name']['id'])) {
            $band = Band::query()->find($attributes['name']['id']);
            if (!$band->user_id) {
//                dd($attributes['name']['name']);
                $band->update(
                    ['name' => $attributes['name']['name']] +
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

        $this->addImage($band, $attributes['cover_file'], 'cover');
        $this->addImage($band, $attributes['logo_file'], 'logo');
    }
}
