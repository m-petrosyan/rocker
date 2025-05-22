<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public static function userBlogs($user)
    {
        return $user->blogs()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    public static function count(): int
    {
        return Blog::query()->count();
    }
}
