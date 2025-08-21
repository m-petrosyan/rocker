<?php

namespace App\Models;

use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Album extends Model implements HasMedia
{
    use  InteractsWithMedia, MediaTrait;

    protected $fillable = [
        'band_id',
        'title',
        'tracks_count',
        'year',
    ];

    protected $appends = [
        'cover',
        'links',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    public function band(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }

    public function getLinksAttribute(): array
    {
        return $this->links()->get()->toArray();
    }

    public function links(): MorphMany
    {
        return $this->morphMany(Links::class, 'model');
    }

    public function getCoverAttribute(): array
    {
        return $this->getImage('cover');
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
}
