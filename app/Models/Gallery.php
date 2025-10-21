<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'views',
        'allViews',
        'total_mb',
        'images_url',
        'cover_img',
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

    public function getCoverImgAttribute(): array
    {
        $cover = Media::query()->find($this->cover);

        return $cover
            ? [
                'thumb' => $cover->getUrl('thumb'),
                'large' => $cover->getUrl('large'),
                'original' => $cover->getUrl(),
            ]
            : [
                'thumb' => isset($this->getImagesUrlAttribute()[0]['thumb'])
                    ? $this->getImagesUrlAttribute()[0]['thumb'] : null,
                'original' => isset($this->getImagesUrlAttribute()[0]['original'])
                    ? $this->getImagesUrlAttribute()[0]['original'] : null,
            ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getImagesUrlAttribute(): array
    {
        $mediaItems = $this->getMedia('images');

        return $mediaItems->isEmpty() ? [] : $mediaItems->map(fn(Media $media) => [
            'id' => $media->id,
            'large' => $media->getUrl('large'),
            'thumb' => $media->getUrl('thumb'),
            'original' => $media->getUrl(),
        ])->toArray();
    }

    public function getTotalMbAttribute(): float
    {
        return round($this->getMedia('images')->sum('size') / 1024 / 1024);
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(350)
            ->height(350)
            ->quality(80)
            ->sharpen(7)
            ->optimize()
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('large')
            ->height(900)
            ->quality(90);
    }
}
