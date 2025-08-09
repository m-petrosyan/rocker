<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model implements Viewable
{
    use ViewsTrait;

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
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class);
    }
}
