<?php

namespace App\Services;

use App\Traits\ComponentServiceTrait;

class UserService
{
    use ComponentServiceTrait;

    public function update(array $attributes): void
    {
        $user = auth()->user();
        $user->update(array_filter($attributes) + ['info' => $attributes['info']]);
        $user->settings->update(
            array_filter($attributes) + [
                'info' => $attributes['info'],
                'city' => $attributes['city'],
                'country' => $attributes['country'],
                'genre' => $attributes['genre'],
                'events_notifications' => $attributes['events_notifications'],
            ]
        );
 
        $this->updateLinks($user, $attributes['links'] ?? []);
    }
}
