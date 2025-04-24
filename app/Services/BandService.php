<?php

namespace App\Services;

use App\Models\Band;
use App\Traits\ComponentServiceTrait;

class BandService
{
    use ComponentServiceTrait;

    public function store(array $attrobites)
    {
        if (isset($attrobites['name']['id'])) {
            $band = Band::query()->find($attrobites['name']['id']);
            if (!$band->user_id) {
                $band->update(
                    ['name' => $attrobites['name']['name']] +
                    $attrobites
                );
            } else {
                abort('403', 'You are not allowed to edit this band');
            }
        } else {
            $band = Band::query()->create($attrobites);
        }

        $this->addImage($band, $attrobites['cover_file'], 'cover');
        $this->addImage($band, $attrobites['logo_file'], 'logo');
    }
}
