<?php

namespace App\Models;

use App\Enums\EventStatusEnum;
use App\Traits\MediaTrait;
use Carbon\Carbon;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Event extends Model implements HasMedia, Viewable
{
    use InteractsWithMedia, InteractsWithViews, MediaTrait;

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
        'end_date',
        'start_time',
        'notify_count',
        'tg_source_chat_id',
        'tg_source_message_id',
    ];

    protected $casts = [
        'cordinates' => 'json',
        'end_date' => 'date:Y-m-d',
    ];

    protected $appends = [
        'poster',
        'start_date_short',
        'status_name',
        'status_text',
        'date_time',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'status_id',

    ];

    protected array $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    public function refreshNotifyCount($count): void
    {
        $this->update(['notify_count' => $count]);
    }

    public function getStatusNameAttribute(): string
    {
        return strtolower(EventStatusEnum::from($this->status?->status)->name);
    }

    public function getStatusTextAttribute()
    {
        return $this->status?->reason ?? '';
    }

    public function getDateTimeAttribute(): bool|Carbon|null
    {
        $startDate = $this->attributes['start_date'] ?? null;
        $startTime = $this->attributes['start_time'] ?? null;

        if (! $startDate) {
            return null;
        }

        if ($startTime) {
            return Carbon::parse($startDate.' '.$startTime);
        }

        return Carbon::parse($startDate);
    }

    public function getStartTimeAttribute($value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('H:i');
    }

    public function getStartDateShortAttribute(): ?string
    {
        $startDate = $this->attributes['start_date'] ?? null;
        if (! $startDate) {
            return null;
        }

        $date = Carbon::createFromFormat('Y-m-d', $startDate);

        return $date ? $date->format('d.m.y') : null;
    }

    public function getEndTimeAttribute($value): ?string
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_messages')->withPivot(['user_id', 'message_id']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class);
    }

    public function status(): HasOne
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
            ->width(400)
            ->quality(80)
            ->sharpen(7)
            ->optimize()
            ->nonQueued()
            ->format('webp');

        $this->addMediaConversion('large')
            ->width(700)
            ->quality(80)
            ->nonQueued()
            ->format('webp');
    }
}
