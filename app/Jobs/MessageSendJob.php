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
            Log::error("MessageSendJob: No chat for user {$this->userId}");

            return;
        }

        // Определяем, шлем мы фото или только текст
        $sendPhoto = false;
        $path = '';

        if ($this->imageUrl) {
            $path = str_starts_with($this->imageUrl, 'http') ? $this->imageUrl : public_path($this->imageUrl);
            if (str_starts_with($path, 'http') || file_exists($path)) {
                $sendPhoto = true;
            } else {
                Log::warning("MessageSendJob: Image not found at {$path}");
            }
        }

        // Отправка
        if ($sendPhoto) {
            $msg = $chat->photo($path)->html($this->message)->send();
        } else {
            $msg = $chat->html($this->message)->send();
        }

        Log::info('TG PRODUCTION TEST', [
            'user_id' => $this->userId,
            'sent_as_photo' => $sendPhoto,
            'path' => $path,
            'status' => $msg->getStatusCode(),
            'response' => (string) $msg->getBody(),
        ]);

        $messageId = $msg?->telegraphMessageId();

        if ($messageId) {
            Log::info("TG SUCCESS: user {$user->id}, message {$messageId}");
        } else {
            Log::error("TG FAILED: user {$user->id}, response: ".(string) $msg->getBody());
        }
    }
}
