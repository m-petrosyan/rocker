<?php

namespace App\Traits;

trait MediaTrait
{
    public function getImage($name): array
    {
        $mediaData = $this->getFirstMedia($name);

        return [
            'id' => $mediaData?->id,
            'large' => $mediaData?->getUrl('large'),
            'thumb' => $mediaData?->getUrl('thumb'),
            'original' => $mediaData?->getUrl(),
        ];
    }
}
