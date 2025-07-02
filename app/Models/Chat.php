<?php

namespace App\Models;

use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends TelegraphChat
{
//    protected $fillable = [
//        'chat_id',
//        'name',
//        'user_id',
//    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }
}
