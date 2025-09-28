<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\EventFormatingTrait;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EventNotificationJob implements ShouldQueue
{
    use Queueable, EventFormatingTrait;

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
//        $this->content = $content;
//        $this->buttons = $buttons;
//        $this->user = $user;
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

        $content = $this->getEventContent($this->event);

        $buttons[] = Button::make('Add to favorites')
            ->action('add_to_favorite')
            ->param('eventId', $this->event->id);

        $users = User::whereNull('email')
            ->has('chat')
            ->with('chat')
            ->get();

        foreach ($users as $user) {
            $user->chat
                ->photo($this->event->poster['large'])
                ->html($content)
                ->keyboard(Keyboard::make()->buttons($buttons))
                ->send();
        }
    }
}
