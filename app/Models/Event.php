<?php

namespace App\Models;

use App\Traits\MediaTrait;
use App\Traits\ViewsTrait;
use Carbon\Carbon;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements Viewable, HasMedia
{
    use ViewsTrait, MediaTrait, InteractsWithMedia;

    protected $fillable = [
        'title',
        'content',
        'link',
        'type',
        'ticket',
        'price',
        'genre',
        'country',
        'city',
        'location',
        'cordinates',
        'start_date',
        'start_time',
        'notify_count',
    ];

    protected $casts = [
        'cordinates' => 'json',
    ];

    protected $appends = [
        'views',
        'allViews',
        'poster',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    public function getDateTimeAttribute(): bool|Carbon
    {
        return Carbon::createFromFormat('Y-m-d,H:i', $this->attributes['start_date'].','.$this->start_time);
    }

    public function getStartTimeAttribute($value): string
    {
        return Carbon::parse($value)->format('H:i');
    }

    public function getStartDateAttribute($value): string
    {
        $date = Carbon::createFromFormat('Y-m-d', $value);

        return $date->format('d.m.y');
    }

    public function getEndTimeAttribute($value): string|null
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class);
    }

    public function confirm(): HasOne
    {
        return $this->hasOne(EventStatus::class);
    }

    public function getPosterAttribute(): array
    {
        return $this->getImage('poster');
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
