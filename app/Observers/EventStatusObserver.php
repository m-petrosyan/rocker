<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Jobs\EventNotificationDeleteJob;
use App\Jobs\EventNotificationJob;
use App\Models\EventStatus;
use App\Models\User;
use App\Traits\UsersBotNotificationTrait;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Support\Facades\Gate;

class EventStatusObserver
{
    use UsersBotNotificationTrait;

    public function created(EventStatus $eventStatus): void
    {
        if (Gate::allows('full-access')) {
            $users = $this->usersList($eventStatus->event);

            foreach ($users as $user) {
                dispatch(new EventNotificationJob($eventStatus->event, $user));
            }
        } else {
            $moderators = User::role(['moderator', 'admin'])->get();
            foreach ($moderators as $user) {
                $user->chat
                    ->message('🎉 new event request')
                    ->keyboard(
                        Keyboard::make()->buttons([
                            Button::make('Event link')->url(route('profile.event.requests', $eventStatus->event->id)),
                        ])
                    )
                    ->send();
            }
        }
    }

    public function updated(EventStatus $eventStatus): void
    {
        if ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::ACCEPTED->value) {
            $users = $this->usersList($eventStatus->event);
            foreach ($users as $user) {
                dispatch(new EventNotificationJob($eventStatus->event, $user));
            }
        } elseif ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::REJECTED->value) {
            $eventStatus->event->user->chat
                ->message("❌ Request to add event rejected, reason: $eventStatus->reason")
                ->send();
        } elseif ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::DELETED->value) {
            $users = $eventStatus->event->notifications()->withPivot('event_id', 'user_id', 'message_id')->get();

            foreach ($users as $user) {
                dispatch(new EventNotificationDeleteJob($eventStatus->event, $user, $user->pivot->message_id));
            }
        }
    }
}
