<?php

namespace App\Repositories;

class BlogRepository
{
    public static function userBlogs($user)
    {
        return $user->blogs()
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }
}
