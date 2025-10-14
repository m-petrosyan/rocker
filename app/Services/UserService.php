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

        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            array_filter($attributes) + [
                'info' => $attributes['info'] ?? null,
                'city' => $attributes['city'] ?? null,
                'country' => $attributes['country'] ?? null,
                'genre' => $attributes['genre'] ?? null,
                'events_notifications' => $attributes['events_notifications'] ?? false,
            ]
        );

        $this->updateLinks($user, $attributes['links'] ?? []);
    }
}
