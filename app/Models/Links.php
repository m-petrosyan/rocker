<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Links extends Model
{
    protected $fillable = [
        'url',
    ];

    public function bands(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }
}
