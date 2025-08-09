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
            'svg' => $mediaData?->mime_type === 'image/svg+xml' ? $mediaData->getUrl() : null,
        ];
    }
}
