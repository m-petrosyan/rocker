<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Band extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'genre',
        'info',
        'user_id',
    ];

    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class);
    }
}
