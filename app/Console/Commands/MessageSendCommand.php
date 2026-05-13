<?php

namespace App\Console\Commands;

use App\Jobs\MessageSendJob;
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
        $message = str_replace('\n', "\n", $this->ask('Message text (use \n for new lines)'));

        $to = $this->choice('Send to', ['user', 'all users']);

        $imageUrl = $this->ask('Image path in public/ (e.g. images/photo.jpg) or press Enter to skip');

        if ($imageUrl) {
            $fullPath = public_path($imageUrl);
            if (! file_exists($fullPath)) {
                $this->error("File not found: $fullPath");
                if (! $this->confirm('Send without image?')) {
                    return;
                }
                $imageUrl = null;
            } else {
                $this->info("Image confirmed: $fullPath");
            }
        }

        if ($to === 'user') {
            $userId = $this->ask('User id');
            $user = User::findOrFail($userId);
            dispatch(new MessageSendJob($message, $user->id, $imageUrl));
        } else {
            $users = User::all();
            foreach ($users as $user) {
                dispatch(new MessageSendJob($message, $user->id, $imageUrl));
            }
            Log::info('message sent to users: '.$users->count());
        }
    }
}
