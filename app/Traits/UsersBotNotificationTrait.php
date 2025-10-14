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
                    // Проверяем страну (всегда)
                    ->where(function ($q) use ($event) {
                        $q->where('country', 'all')
                            ->orWhere('country', $event->country);
                    })
                    // Проверяем город только если страна не all
                    ->where(function ($q) use ($event) {
                        $q->where('country', 'all') // если country = all → пропускаем city
                        ->orWhere(function ($q2) use ($event) {
                            $q2->where(function ($q3) use ($event) {
                                $q3->where('city', 'all')
                                    ->orWhere('city', $event->city);
                            });
                        });
                    })
                    ->when($event->genre !== 'all', function ($query) use ($event) {
                        $query->where(function ($q) use ($event) {
                            $q->where('genre', 'all')
                                ->orWhere('genre', $event->genre);
                        });
                    });
            })
            ->with('chat', 'chat.bot', 'settings')
            ->get();
    }
}
