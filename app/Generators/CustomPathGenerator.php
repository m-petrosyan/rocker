<?php

namespace App\Generators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    private function getCustomPath(Media $media)
    {
        return defined($media->model_type.'::MEDIA_PATH')
            ? $media->model_type::MEDIA_PATH
            : (new $media->model_type)->getTable();
    }

    public function getPath(Media $media): string
    {
        return $this->getCustomPath($media)
            .'/'.$media->model_id.'/'.$media->id.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsives/';
    }
}
