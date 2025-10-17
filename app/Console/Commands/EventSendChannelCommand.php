<?php

namespace App\Console\Commands;

use App\Jobs\EventCahnnelNotificationJob;
use App\Models\Event;
use Illuminate\Console\Command;

class EventSendChannelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-send-channel-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $id = $this->ask('Event id');
        $event = Event::findOrFail($id);


        dispatch(new EventCahnnelNotificationJob($event));
    }
}
