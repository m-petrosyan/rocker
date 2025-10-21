<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfilesMergeRequest;
use App\Models\BotMergeCode;
use App\Models\Event;
use App\Models\EventStatus;
use App\Models\User;
use App\Models\UserBot;
use App\Models\UserSettings;
use Illuminate\Support\Facades\DB;

class MergeProfilesController extends Controller
{
    public function mergeBot(ProfilesMergeRequest $request)
    {
        $mergeCode = BotMergeCode::where('code', $request->code)->first();

        $user1 = auth()->user();
        $user2 = User::findOrFail($mergeCode->user_id);

        $primaryUser = $user1->email ? $user1 : $user2;
        $secondaryUser = $primaryUser->id === $user1->id ? $user2 : $user1;

        DB::transaction(function () use ($primaryUser, $secondaryUser, $mergeCode) {
            Event::where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            UserSettings::where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            EventStatus::where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            UserBot::where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            DB::table('event_user_favorites')
                ->where('user_id', $secondaryUser->id)
                ->update(['user_id' => $primaryUser->id]);

            $primaryUser->last_activity = collect([
                $primaryUser->last_activity,
                $secondaryUser->last_activity,
            ])->filter()->max();


            $primaryUser->created_at = collect([
                $primaryUser->created_at,
                $secondaryUser->created_at,
            ])->filter()->min();


            $primaryUser->updated_at = collect([
                $primaryUser->updated_at,
                $secondaryUser->updated_at,
            ])->filter()->min();
            
            $exists = User::where('username', $secondaryUser->username)->exists();
            if (!$exists) {
                $primaryUser->username = $secondaryUser->username;
            }

            $primaryUser->save();

            $secondaryUser->delete();
            $mergeCode->delete();
        });

        session()->flash('message', 'Your Telegram account has been linked successfully!');
    }

    public function getCode()
    {
        BotMergeCode::where('user_id', auth()->id())->delete();

        $code = random_int(100000, 999999);

        BotMergeCode::create([
            'user_id' => auth()->id(),
            'code' => $code,
        ]);

        session()->flash('message', 'Your merge code is: '.$code);
    }
}
