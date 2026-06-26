<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminPendingEventNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected int $eventId) {}

    public function handle(): void
    {
        $event = Event::with('media')->find($this->eventId);
        if (! $event) {
            return;
        }

        $admins = User::role('admin')
            ->with('chat')
            ->get()
            ->filter(fn (User $u) => $u->chat !== null);

        if ($admins->isEmpty()) {
            Log::warning('AdminPendingEventNotificationJob: нет админов с привязанным ботом');

            return;
        }

        $caption = $this->buildCaption($event);
        $buttons = $this->buildButtons($event);
        $poster = $event->poster['large'] ?? null;

        foreach ($admins as $admin) {
            try {
                $message = $admin->chat;

                if ($poster) {
                    $message = $message->photo($poster);
                }

                $message
                    ->html($caption)
                    ->keyboard(Keyboard::make()->buttons($buttons))
                    ->send();
            } catch (\Throwable $e) {
                Log::error('AdminPendingEventNotificationJob: ошибка отправки', [
                    'admin_id' => $admin->id,
                    'event_id' => $event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function buildCaption(Event $event): string
    {
        $flag = $event->country === 'am' ? '🇦🇲' : '🇬🇪';
        $preview = Str::limit(strip_tags($event->content), 600);

        return implode("\n", [
            '🆕 <b>Новый pending Event</b>',
            "📍 {$event->city} {$flag}",
            "🔗 Источник: <code>{$event->tg_source_chat_id}/{$event->tg_source_message_id}</code>",
            '',
            $preview,
        ]);
    }

    private function buildButtons(Event $event): array
    {
        return [
            Button::make('✏️ Edit')
                ->webApp(route('profile.events.edit', $event->id)),
        ];
    }
}
