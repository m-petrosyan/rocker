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

    public function getAllViewsAttribute(): int
    {
        return $this->allViewsCount();
    }

    public function viewsCount(): int
    {
        return views($this)
            ->unique()
            ->count() ?? 0;
    }

    public function allViewsCount(): int
    {
        return views($this)
            ->count() ?? 0;
    }
}
