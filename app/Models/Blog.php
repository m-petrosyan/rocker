<?php

namespace App\Models;

use App\Traits\ViewsTrait;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model implements Viewable
{
    use  ViewsTrait;

    //
}
