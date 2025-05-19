<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;
use Illuminate\Support\Str;

class BlogService
{
    use ComponentServiceTrait;

    public function store(array $attributes): void
    {
        $blog = auth()->user()->blogs()->create($attributes);

        $blog->slug = Str::slug($attributes['title']['en'] ?? $attributes['title']['am']);
        $blog->save();

        if (isset($attributes['cover_file'])) {
            $this->addImage($blog, $attributes['cover_file'], 'cover');
        }

        $this->addSyncBand($blog, $attributes);
    }

    public function update($blog, $attributes): void
    {
        $blog->update($attributes);

        $this->addSyncBand($blog, $attributes);

        if (isset($attributes['cover_file'])) {
            $blog->clearMediaCollection('cover');
            $this->addImage($blog, $attributes['cover_file'], 'cover');
        }
    }

    public function destroy($blog): void
    {
        $blog->clearMediaCollection('cover');

        $blog->delete();
    }
}
