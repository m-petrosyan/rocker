<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('tg_source_chat_id', 32)->nullable()->after('notify_count');
            $table->unsignedBigInteger('tg_source_message_id')->nullable()->after('tg_source_chat_id');

            $table->unique(['tg_source_chat_id', 'tg_source_message_id'], 'events_tg_source_unique');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique('events_tg_source_unique');
            $table->dropColumn(['tg_source_chat_id', 'tg_source_message_id']);
        });
    }
};
