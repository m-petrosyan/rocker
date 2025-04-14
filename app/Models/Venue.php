<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'cid',
        'title',
        'address',
        'cordinates',
    ];

    protected $casts = [
        'cordinates' => 'array',
    ];

    public function getCordinatesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setCordinatesAttribute($value): void
    {
        $this->attributes['cordinates'] = json_encode($value);
    }
}
