<?php

namespace App\Models;

use App\Traits\MediaTrait;
use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
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
    ];

    protected $appends = [
        'logo',
        'cover',
        'views',
    ];

    protected $hidden = [
        'media',
    ];

    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
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
