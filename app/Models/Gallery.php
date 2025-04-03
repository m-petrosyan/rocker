<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model implements Viewable
{
    use  ViewsTrait;

    protected $fillable = [
        'user_id',
        'title',
    ];
}
