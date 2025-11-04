<?php

namespace App\Console\Commands;

use App\Jobs\EventCahnnelNotificationJob;
use App\Jobs\EventNotificationJob;
use App\Models\Event;
use App\Models\User;
use Illuminate\Console\Command;

class EventSendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-send-command';

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

        $to = $this->choice('Send to', ['user', 'channel']);

        dd($this->usersList($event));

        if ($to === 'user') {
            $userId = $this->ask('User id');
            $user = User::findOrFail($userId);
            dispatch(new EventNotificationJob($event, $user));
        } else {
            dispatch(new EventCahnnelNotificationJob($event));
        }
    }
}
