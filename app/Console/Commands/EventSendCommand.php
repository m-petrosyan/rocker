<?php

namespace App\Console\Commands;

use App\Jobs\EventCahnnelNotificationJob;
use App\Jobs\EventNotificationJob;
use App\Models\Event;
use App\Models\User;
use App\Traits\UsersBotNotificationTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EventSendCommand extends Command
{
    use UsersBotNotificationTrait;

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

        $to = $this->choice('Send to', ['user', 'all users', 'channel']);

        if ($to === 'user') {
            $userId = $this->ask('User id');
            $user = User::findOrFail($userId);
            dispatch(new EventNotificationJob($event, $user));
        } else {
            if ($to === 'all users') {
                $users = $this->usersList($event);
                foreach ($users as $user) {
                    dispatch(new EventNotificationJob($event->id, $user->id));
                }
                Log::info('user '.$users[0]);
            } else {
                if ($to === 'channel') {
                    dispatch(new EventCahnnelNotificationJob($event->id));
                }
            }
        }
    }
}
