<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('fb_page_url')->nullable()->after('events_concerts');
            $table->string('fb_page_id')->nullable()->after('fb_page_url');
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['fb_page_url', 'fb_page_id']);
        });
    }
};
