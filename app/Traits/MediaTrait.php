<?php

namespace App\Traits;

trait MediaTrait
{
    public function getImage($name, array|null $fields = null): array
    {
        $mediaData = $this->getFirstMedia($name);

        $result = [
            'id' => $mediaData?->id,
        ];

        $defaultFields = ['large', 'thumb', 'original', 'svg'];

        $fieldsToProcess = $fields ?? $defaultFields;

        foreach ($fieldsToProcess as $field) {
            if (in_array($field, $defaultFields)) {
                if ($field === 'svg') {
                    $result['svg'] = $mediaData?->mime_type === 'image/svg+xml' ? $mediaData->getUrl() : null;
                } elseif ($field === 'original') {
                    $result[$field] = $mediaData?->getUrl();
                } else {
                    $result[$field] = $mediaData?->getUrl($field);
                }
            }
        }

        return $result;
    }
}
