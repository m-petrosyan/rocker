<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id');
            $table->string('name')->nullable();

            $table->foreignId('bot_id')->constrained('bots')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['chat_id', 'bot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
