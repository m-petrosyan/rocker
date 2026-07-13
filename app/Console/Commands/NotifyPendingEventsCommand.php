<?php

namespace App\Console\Commands;

use App\Enums\EventStatusEnum;
use App\Jobs\AdminPendingEventNotificationJob;
use App\Models\Event;
use Illuminate\Console\Command;

class NotifyPendingEventsCommand extends Command
{
    protected $signature = 'app:notify-pending-events';

    protected $description = 'Отправить админам уведомления о новых pending Events, созданных за сегодня';

    public function handle(): int
    {
        $pendingEvents = Event::query()
            ->whereHas('status', fn ($q) => $q->where('status', EventStatusEnum::PENDING->value))
            ->whereDate('created_at', today())
            ->get();

        if ($pendingEvents->isEmpty()) {
            $this->info('Нет новых pending Events за сегодня.');

            return self::SUCCESS;
        }

        $this->info('Найдено pending Events: '.$pendingEvents->count());

        foreach ($pendingEvents as $event) {
            dispatch(new AdminPendingEventNotificationJob($event->id));
            $this->line("📨 Уведомление для Event #{$event->id} — «{$event->title}»");
        }

        $this->newLine();
        $this->info('Готово: отправлено уведомлений — '.$pendingEvents->count());

        return self::SUCCESS;
    }
}
