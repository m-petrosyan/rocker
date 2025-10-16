<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bot extends TelegraphBot
{
    public function chats(): HasMany
    {
        return $this->hasMany(UserBot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
