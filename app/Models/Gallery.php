<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Gallery extends Model implements Viewable, HasMedia
{
    use  InteractsWithMedia, ViewsTrait;

    protected $fillable = [
        'user_id',
        'description',
        'date',
        'title',
    ];

    protected $appends = [
        'images',
        'cover',
    ];
    protected $hidden = [
        'media',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverAttribute()
    {
        return $this->getImagesAttribute()[0] ?? null;
    }

    public function getImagesAttribute(): array
    {
        $mediaItems = $this->getMedia('images');

        if ($mediaItems->isEmpty()) {
            return [];
        }

        return $mediaItems->map(function (Media $media) {
            return [
                'id' => $media->id,
                'large' => $media->getUrl('large'),
                'thumb' => $media->getUrl('thumb'),
                'original' => $media->getUrl(),
            ];
        })->toArray();
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(210);

        $this->addMediaConversion('large')
            ->width(1920);
    }
}
