<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'city',
        'genre',
        'language_code',
        'events_notifications',
        'events_concerts',
    ];

}
