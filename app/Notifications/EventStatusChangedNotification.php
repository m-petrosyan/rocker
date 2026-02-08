<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventStatusChangedNotification extends Notification
{
    use Queueable;

    protected string $eventTitle;
    protected string $eventUrl;
    protected string $status;
    protected ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $eventTitle, string $eventUrl, string $status, ?string $reason = null)
    {
        $this->eventTitle = $eventTitle;
        $this->eventUrl = $eventUrl;
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'event_status_changed',
            'entity_type' => 'event',
            'entity_title' => $this->eventTitle,
            'entity_url' => $this->eventUrl,
            'status' => $this->status,
            'reason' => $this->reason,
            'message' => $this->getMessage(),
        ];
    }

    protected function getMessage(): string
    {
        return match ($this->status) {
            'accepted' => "🎉 Great news! Your event \"{$this->eventTitle}\" has been approved!",
            'rejected' => "❌ Unfortunately, your event \"{$this->eventTitle}\" was rejected." . ($this->reason ? " Reason: {$this->reason}" : ''),
            'deleted' => "Your event \"{$this->eventTitle}\" has been deleted.",
            default => "The status of your event \"{$this->eventTitle}\" has been changed to {$this->status}.",
        };
    }
}
