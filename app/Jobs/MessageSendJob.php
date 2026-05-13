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
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId)->load('chat');

        $msg = $user->chat
            ->html(str_replace('\n', "\n", $this->message))
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
