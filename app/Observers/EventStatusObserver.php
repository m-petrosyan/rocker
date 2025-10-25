<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Jobs\EventCahnnelNotificationJob;
use App\Jobs\EventChannelNotification;
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
//        dd(Gate::allows('full-access'));
        if (Gate::allows('full-access')) {
            $users = $this->usersList($eventStatus->event);

            foreach ($users as $user) {
                dispatch(new EventNotificationJob($eventStatus->event, $user));
            }

            dispatch(new EventCahnnelNotificationJob($eventStatus->event));

            $eventStatus->event->refreshNotifyCount($this->usersList($eventStatus->event, true));
        } else {
            $moderators = User::role(['moderator', 'admin'])->whereHas('chat')->get();
            foreach ($moderators as $user) {
                $user->chat
                    ->message('🎉 new event request')
                    ->keyboard(
                        Keyboard::make()->buttons([
                            Button::make('Event link')->webApp(
                                route('profile.event.requests', $eventStatus->event->id)
                            ),
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

            dispatch(new EventCahnnelNotificationJob($eventStatus->event));

            $usersCount = $this->usersList($eventStatus->event, true);

            $eventStatus->event->refreshNotifyCount($usersCount);

            $eventStatus->event->user?->chat
                ->message("Thank you! The event has been added and will be sent to {$usersCount} 🤘 people.")
                ->send();
        } elseif ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::REJECTED->value) {
            $eventStatus->event->user?->chat
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
