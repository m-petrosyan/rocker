<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MargeUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:marge-users-command';

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
        $mainUserId = $this->ask('Main User id');
        $secondaryUserId = $this->ask('Secondary User id');

        $user1 = User::find($mainUserId);
        $user2 = User::find($secondaryUserId);

        $primaryUser = $user1->email ? $user1 : $user2;
        $secondaryUser = $primaryUser->id === $user1->id ? $user2 : $user1;

        DB::transaction(function () use ($primaryUser, $secondaryUser) {
            Event::where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            DB::table('user_bots')
                ->where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            DB::table('user_settings')
                ->where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            DB::table('event_status')
                ->where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            DB::table('event_user_favorites')
                ->where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            $primaryUser->last_activity_updated_at = max(
                $primaryUser->last_activity_updated_at?->timestamp ??
                $secondaryUser->last_activity_updated_at?->timestamp
            );

            $primaryUser->created_at = min(
                $primaryUser->created_at?->timestamp ??
                $secondaryUser->created_at?->timestamp
            );

            $exists = User::where('username', $secondaryUser->username)->exists();
            if (!$exists) {
                $primaryUser->username = $secondaryUser->username;
            }

            $primaryUser->save();

            $secondaryUser->delete();
        });
    }
}
