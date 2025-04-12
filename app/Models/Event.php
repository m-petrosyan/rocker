<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;

class Event extends Model implements Viewable
{
    use ViewsTrait;

    protected $fillable = [
        'user_id',
        'event_id',
        'notify_count',
    ];
}
