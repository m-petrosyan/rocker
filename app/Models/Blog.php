<?php

namespace App\Models;

use App\Traits\MediaTrait;
use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Blog extends Model implements Viewable, HasMedia
{
    use ViewsTrait, HasTranslations, MediaTrait, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'slug',
        'author',
    ];

    protected $appends = [
        'cover',
        'views',
        'bands',
    ];

    public array $translatable = ['title', 'description', 'content'];

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBandsAttribute()
    {
        return $this->bands()->get();
    }

    public function getCoverAttribute(): array
    {
        return $this->getImage('cover');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->quality(80)
            ->sharpen(7)
            ->optimize()
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1280)
            ->quality(90)
            ->format('webp')
            ->nonQueued();
    }
}
