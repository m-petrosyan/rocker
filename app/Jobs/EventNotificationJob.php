<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
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
    public function __construct(protected int $eventId, protected int $userId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = Event::findOrFail($this->eventId)->load('media');
        $user = User::findOrFail($this->userId)->load('chat');

        $content = $this->getEventContent($event);
        $buttons = $this->getButtons($event);


        Log::info('poster', [$event->poster['large']]);

        $msg = $user->chat
            ->photo($event->poster['large'])
            ->html($content)
            ->keyboard(Keyboard::make()->buttons($buttons))
            ->send();

        Log::info('TG RAW RAW', [
            'status' => $msg->getStatusCode(),
            'body' => (string)$msg->getBody(),
        ]);

        $messageId = $msg?->telegraphMessageId();

        if ($messageId) {
            $event->notifications()->syncWithoutDetaching([
                $user->chat->id => [
                    'user_id' => $user->id,
                    'message_id' => $messageId,
                ],
            ]);
            Log::info("TG send OK user {$user->id} message {$messageId}");
        } else {
            Log::warning("NO MESSAGE ID user {$user->id}");
        }
    }
}
