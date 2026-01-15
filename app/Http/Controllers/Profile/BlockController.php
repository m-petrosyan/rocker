<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserBlockedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        if (!auth()->user()->hasAnyRole(['admin', 'moderator'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot block yourself.');
        }

        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Moderators cannot block administrators.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($user->is_blocked) {
            return back()->with('error', 'The user has already been blocked.');
        }

        $user->blockedRecord()->create([
            'blocked_by' => auth()->id(),
            'reason' => $request->reason,
        ]);

        if ($user->email) {
            $user->notify(new UserBlockedNotification($request->reason));
        }

        return back()->with('message', 'The user has been successfully blocked.');
    }
}
