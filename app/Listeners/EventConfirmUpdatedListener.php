<?php

namespace App\Listeners;

use App\Jobs\SendMessageJob;
use App\Models\User;

class EventConfirmUpdatedListener
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $eventConfirm): void
    {
        $this->send_new_event($eventConfirm->eventConfirm->event);

        $user = $eventConfirm->eventConfirm->event->user;

        if (!$user->isAdmin() || !$user->isModerator()) {
            $notify_count = $eventConfirm->eventConfirm->event->notify_count;

            $message = "🤘Thank you, the event has been added and was sent to $notify_count people.";

            User::findOrFail(1)->load('chat')->chat->message('accept')->send();

            dispatch(new SendMessageJob($user, $message));
        }
    }
}
