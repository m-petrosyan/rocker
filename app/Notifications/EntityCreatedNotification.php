<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EntityCreatedNotification extends Notification
{
    use Queueable;

    protected string $entityType;
    protected string $entityTitle;
    protected string $entityUrl;
    protected string $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $entityType, string $entityTitle, string $entityUrl, string $status = 'pending')
    {
        $this->entityType = $entityType;
        $this->entityTitle = $entityTitle;
        $this->entityUrl = $entityUrl;
        $this->status = $status;
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
            'type' => 'entity_created',
            'entity_type' => $this->entityType,
            'entity_title' => $this->entityTitle,
            'entity_url' => $this->entityUrl,
            'status' => $this->status,
            'message' => $this->getMessage(),
        ];
    }

    protected function getMessage(): string
    {
        $messages = [
            'event' => [
                'pending' => "Your event \"{$this->entityTitle}\" is awaiting moderation.",
                'approved' => "Your event \"{$this->entityTitle}\" has been published.",
            ],
            'gallery' => [
                'approved' => "Your gallery \"{$this->entityTitle}\" has been created.",
            ],
            'blog' => [
                'approved' => "Your blog post \"{$this->entityTitle}\" has been created.",
            ],
            'band' => [
                'approved' => "Your band \"{$this->entityTitle}\" has been created.",
            ],
        ];

        return $messages[$this->entityType][$this->status] ?? "Your {$this->entityType} has been created.";
    }
}
