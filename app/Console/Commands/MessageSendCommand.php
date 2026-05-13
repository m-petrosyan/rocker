<?php

namespace App\Console\Commands;

use App\Jobs\EventNotificationJob;
use App\Models\User;
use App\Traits\UsersBotNotificationTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MessageSendCommand extends Command
{
    use UsersBotNotificationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:message-send-command';

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
        $message = $this->ask('Message text');

        $to = $this->choice('Send to', ['user', 'all users']);

        if ($to === 'user') {
            $userId = $this->ask('User id');
            $user = User::findOrFail($userId);
            dispatch(new EventNotificationJob($message, $user->id));
        } else {
            $users = User::all();
            foreach ($users as $user) {
                dispatch(new EventNotificationJob($message, $user->id));
            }
            Log::info('message sent to users: '.$users->count());
        }
    }
}
