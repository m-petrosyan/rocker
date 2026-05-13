<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class MessageSendJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $message,
        protected int $userId,
        protected ?string $imageUrl = null,
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId)->load('chat');
        $chat = $user->chat;

        if ($this->imageUrl) {
            $chat->photo($this->imageUrl)->send();
        }

        $msg = $chat
            ->html($this->message)
            ->send();

        Log::info('TG RAW', [
            'status' => $msg->getStatusCode(),
            'body' => (string) $msg->getBody(),
        ]);

        $messageId = $msg?->telegraphMessageId();

        if ($messageId) {
            Log::info("TG send OK user {$user->id} message {$messageId}");
        } else {
            Log::warning("NO MESSAGE ID user {$user->id}");
        }
    }
}
