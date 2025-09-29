<?php

namespace App\Jobs;

use App\Traits\EventFormatingTrait;
use App\Traits\UsersBotNotificationTrait;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EventNotificationJob implements ShouldQueue
{
    use Queueable, EventFormatingTrait, UsersBotNotificationTrait;

    protected object $event;
    protected object $user;
    protected object $content;
    protected array $buttons;

    /**
     * Create a new job instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        $msg = $this->send_events_list($this->event, $this->content, $this->buttons, $this->user);

//        $this->event->notifications()->syncWithoutDetaching([
//            $this->user->id => ['message_id' => $msg->telegraphMessageId()],
//        ]);
//        sleep(3);
        $content = $this->getEventContent($this->event);

        $buttons = $this->getButtons($this->event);

        $users = $this->usersList($this->event);
//        info(json_encode($this->event->poster['large']));
        foreach ($users as $user) {
            $user->chat
                ->photo($this->event->poster['thumb'])
                ->html($content)
                ->keyboard(Keyboard::make()->buttons($buttons))
                ->send();
        }
    }
}
