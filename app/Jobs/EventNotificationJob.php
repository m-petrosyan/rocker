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

        try {
            retry(3, function () use ($content, $buttons) {
                $msg = $this->user->chat
                    ->photo($this->event->poster['large'])
                    ->html($content)
                    ->keyboard(Keyboard::make()->buttons($buttons))
                    ->send();

                Log::info('TG RAW RAW', [
                    'status' => $msg->getStatusCode(),
                    'body' => (string)$msg->getBody(),
                ]);

                Log::info($this->event->poster['large']);
                $messageId = $msg?->telegraphMessageId();

                if ($messageId) {
                    $this->event->notifications()->syncWithoutDetaching([
                        $this->user->chat->id => [
                            'user_id' => $this->user->id,
                            'message_id' => $messageId,
                        ],
                    ]);
                    Log::info("TG send OK user {$this->user->id} message {$messageId}");
                } else {
                    Log::warning("NO MESSAGE ID user {$this->user->id}");
                }
            }, 1000);
        } catch (\Throwable $e) {
            Log::error("TG send FAIL user={$this->user->id}: {$e->getMessage()}");
        }
    }
}
