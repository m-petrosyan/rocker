<?php

namespace App\Traits;

use CyrildeWit\EloquentViewable\InteractsWithViews;

trait ViewsTrait
{
    use InteractsWithViews;

    public function getViewsAttribute(): int
    {
        return $this->viewsCount();
    }

    public function viewsCount(): int
    {
        return views($this)
            ->unique()
            ->count() ?? 0;
    }
}
