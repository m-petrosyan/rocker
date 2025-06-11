<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Gallery extends Model implements Viewable, HasMedia
{
    use InteractsWithMedia, ViewsTrait;

    protected $fillable = [
        'user_id',
        'venue_id',
        'description',
        'date',
        'title',
        'cover',
    ];

    protected $appends = [
        'images_url',
        'total_mb',
        'cover_img',
        'bands',
        'venue',
        'views',
        'allViews',
    ];

    protected $hidden = [
        'media',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class);
    }

    public function getVenueAttribute(): BelongsTo
    {
        return $this->venue();
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getBandsAttribute()
    {
        return $this->bands()->get();
    }


    public function getCoverImgAttribute(): array
    {
        $cover = Media::query()->find($this->cover);

        return $cover
            ? [
                'large' => $cover->getUrl('large'),
                'thumb' => $cover->getUrl('thumb'),
                'original' => $cover->getUrl(),
            ]
            : [
                'thumb' => isset($this->getImagesUrlAttribute()[0]['thumb'])
                    ? $this->getImagesUrlAttribute()[0]['thumb'] : null,
                'large' => isset($this->getImagesUrlAttribute()[0]['large'])
                    ? $this->getImagesUrlAttribute()[0]['large'] : null,
                'original' => isset($this->getImagesUrlAttribute()[0]['original'])
                    ? $this->getImagesUrlAttribute()[0]['original'] : null,
            ];
    }

    public function getImagesUrlAttribute(): array
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

    public function getTotalMbAttribute(): float
    {
        return round($this->getMedia('images')->sum('size') / 1024 / 1024);
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->fit(Fit::Crop, 300, 300)
            ->quality(100)
            ->sharpen(7)
            ->optimize();

        $this->addMediaConversion('large')
            ->width(1920)
            ->quality(90);
    }
}
