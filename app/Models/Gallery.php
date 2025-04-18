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
    use  InteractsWithMedia, ViewsTrait;

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
        'cover_img',
        'bands',
        'venue',
        'views',
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


    public function getCoverImgAttribute()
    {
        return Media::find($this->cover)?->getUrl('thumb')
            ?? $this->getImagesUrlAttribute()[0]['thumb'];
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
