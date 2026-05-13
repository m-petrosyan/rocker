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

        if (! $chat) {
            Log::error("MessageSendJob: User {$this->userId} has no telegram chat connected.");

            return;
        }

        $telegraph = $chat;

        if ($this->imageUrl) {
            if (str_starts_with($this->imageUrl, 'http')) {
                $telegraph = $telegraph->photo($this->imageUrl);
            } else {
                $path = public_path($this->imageUrl);
                if (file_exists($path)) {
                    $telegraph = $telegraph->photo($path);
                } else {
                    Log::warning("MessageSendJob: Local image not found at {$path}");
                }
            }
        }

        $msg = $telegraph->html($this->message)->send();

        Log::info('TG RAW RESPONSE', [
            'user_id' => $this->userId,
            'status' => $msg->getStatusCode(),
            'body' => (string) $msg->getBody(),
        ]);

        $messageId = $msg?->telegraphMessageId();

        if ($messageId) {
            Log::info("TG send OK user {$user->id} message {$messageId}");
        } else {
            Log::warning("NO MESSAGE ID user {$user->id}. Response: ".(string) $msg->getBody());
        }
    }
}
