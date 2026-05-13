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

        Log::info('MessageSendJob: Processing', [
            'user_id' => $this->userId,
            'image_url' => $this->imageUrl,
            'message' => $this->message,
        ]);

        $telegraph = $chat->html($this->message);

        if ($this->imageUrl) {
            $path = public_path($this->imageUrl);
            $exists = file_exists($path);

            Log::info('MessageSendJob: Image check', [
                'path' => $path,
                'exists' => $exists,
            ]);

            if ($exists) {
                $telegraph = $chat->photo($path)->html($this->message);
            } else {
                Log::warning("MessageSendJob: Image not found at {$path}");
            }
        }

        $msg = $telegraph->send();

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
