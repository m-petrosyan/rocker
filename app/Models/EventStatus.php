<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStatus extends Model
{
    protected $fillable = [
        'user_id',
        'confirmed',
        'reason',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
