<?php

namespace App\Jobs;

use App\Traits\EventFormatingTrait;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EventNotificationJob implements ShouldQueue
{
    use Queueable, EventFormatingTrait;


    /**
     * Create a new job instance.
     */
    public function __construct(protected object $event, protected object $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $content = $this->getEventContent($this->event);

        $buttons = $this->getButtons($this->event);

        Log::info($this->event->poster['thumb']);
        $msg = $this->user->chat
            ->photo($this->event->poster['thumb'])
            ->html($content)
            ->keyboard(Keyboard::make()->buttons($buttons))
            ->send();

        $messageId = $msg?->telegraphMessageId();

        if ($messageId) {
            $this->event->notifications()->syncWithoutDetaching([
                $this->user->chat->id => [
                    'user_id' => $this->user->id,
                    'message_id' => $messageId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        } else {
            \Log::warning('Telegram message not sent for user '.$this->user->id);
        }
    }
}
