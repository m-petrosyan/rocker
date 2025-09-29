<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Collection;

trait UsersBotNotificationTrait
{
    public function usersList($event): Collection
    {
        return User::has('chat')
            ->whereHas('settings', function ($query) use ($event) {
                $query->where('events_notifications', true)
                    ->where(function ($query) use ($event) {
                        $query->where('country', 'all')
                            ->orWhere('country', $event->country);
                    })
                    ->where(function ($query) use ($event) {
                        $query->where('city', 'all')
                            ->orWhere('city', $event->city);
                    })
                    ->when($event->genre !== 'all', function ($query) use ($event) {
                        $query->where(function ($query) use ($event) {
                            $query->where('genre', 'all')
                                ->orWhere('genre', $event->genre);
                        });
                    });
            })->with('chat', 'chat.bot', 'settings')->get();
    }
}
