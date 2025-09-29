<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStatus extends Model
{
    protected $table = 'event_status';

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'reason',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
