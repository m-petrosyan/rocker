<?php

namespace App\Observers;

use App\Enums\EventStatusEnum;
use App\Jobs\EventCahnnelNotificationJob;
use App\Jobs\EventNotificationDeleteJob;
use App\Jobs\EventNotificationJob;
use App\Models\EventStatus;
use App\Models\User;
use App\Notifications\EventStatusChangedNotification;
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
                dispatch(new EventNotificationJob($eventStatus->event->id, $user->id))->delay(now()->addSeconds(5));
            }

            if (! $eventStatus->event->tg_source_chat_id) {
                dispatch(new EventCahnnelNotificationJob($eventStatus->event->id));
            }

            $eventStatus->event->refreshNotifyCount($this->usersList($eventStatus->event, true));
        } else {
            $isScraped = (bool) $eventStatus->event->tg_source_chat_id;
            $moderators = User::role(['moderator', 'admin'])->whereHas('chat')->get();

            foreach ($moderators as $user) {
                $msg = $user->chat
                    ->message($isScraped ? '📎new scraped event' : '🎉 new event request')
                    ->keyboard(
                        Keyboard::make()->buttons([
                            Button::make('Event link')->webApp(
                                route('profile.event.requests', $eventStatus->event->id)
                            ),
                        ])
                    );

                if ($isScraped) {
                    $msg->silent();
                }

                $msg->send();
            }

            $eventStatus->event->user?->chat
                ?->message('✅ Your event request has been sent for review. You will be notified once it is processed.')
                ->send();
        }
    }

    public function updated(EventStatus $eventStatus): void
    {
        if ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::ACCEPTED->value) {
            $users = $this->usersList($eventStatus->event);
            foreach ($users as $user) {
                dispatch(new EventNotificationJob($eventStatus->event->id, $user->id));
            }

            if (! $eventStatus->event->tg_source_chat_id) {
                dispatch(new EventCahnnelNotificationJob($eventStatus->event->id));
            }

            $usersCount = $this->usersList($eventStatus->event, true);

            $eventStatus->event->refreshNotifyCount($usersCount);

            $eventStatus->event->user?->chat
                ?->message("Thank you! The event has been added and will be sent to {$usersCount} 🤘 people.")
                ->send();

            // Send in-app notification about event approval
            $eventStatus->event->user?->notify(new EventStatusChangedNotification(
                $eventStatus->event->title,
                route('events.show', $eventStatus->event),
                'accepted'
            ));
        } elseif ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::REJECTED->value) {
            $eventStatus->event->user?->chat
                ?->message("❌ Request to add event rejected, reason: $eventStatus->reason")
                ->send();

            // Send in-app notification about event rejection
            $eventStatus->event->user?->notify(new EventStatusChangedNotification(
                $eventStatus->event->title,
                route('events.show', $eventStatus->event),
                'rejected',
                $eventStatus->reason
            ));
        } elseif ($eventStatus->isDirty('status') && $eventStatus->status === EventStatusEnum::DELETED->value) {
            $users = $eventStatus->event->notifications()->withPivot('event_id', 'user_id', 'message_id')->get();

            foreach ($users as $user) {
                dispatch(new EventNotificationDeleteJob($eventStatus->event, $user, $user->pivot->message_id));
            }
        }
    }
}
