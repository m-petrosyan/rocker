<?php

namespace App\Models;

use App\Traits\MediaTrait;
use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Band extends Model implements Viewable, HasMedia
{
    use  InteractsWithMedia, ViewsTrait, MediaTrait;

    protected $fillable = [
        'name',
        'slug',
        'info',
        'user_id',
        'cover_position',
    ];

    protected $appends = [
        'logo',
        'cover',
        'views',
        'images_url',
    ];

    protected $hidden = [
        'media',
    ];

    protected $casts = [
        'cover_position' => 'array',
    ];


    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Links::class, 'model');
    }

    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getCoverAttribute(): array
    {
        return $this->getImage('cover');
    }

    public function getLogoAttribute(): array
    {
        return $this->getImage('logo');
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
            ->quality(100)
            ->sharpen(7)
            ->optimize()
            ->format('webp');

        $this->addMediaConversion('large')
            ->width(1280)
            ->quality(100)
            ->format('webp');
    }

    public function getCoverPositionAttribute($value)
    {
        return $value ? json_decode($value, true) : ['x' => 50, 'y' => 50];
    }
}
