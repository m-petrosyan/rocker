<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;

class UserService
{
    use ComponentServiceTrait;

    public function update(array $attributes)
    {
        $user = auth()->user();
        $user->update(array_filter($attributes));


        $this->updateLinks($user, $attributes['links'] ?? []);
    }
}
