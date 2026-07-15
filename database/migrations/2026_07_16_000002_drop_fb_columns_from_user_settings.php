<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $columns = ['fb_page_url', 'fb_page_id', 'fb_user_id', 'fb_user_token'];

            foreach ($columns as $col) {
                if (Schema::hasColumn('user_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('user_settings', 'fb_page_url')) {
                $table->string('fb_page_url')->nullable()->after('events_concerts');
            }
            if (! Schema::hasColumn('user_settings', 'fb_page_id')) {
                $table->string('fb_page_id')->nullable()->after('fb_page_url');
            }
            if (! Schema::hasColumn('user_settings', 'fb_user_id')) {
                $table->string('fb_user_id', 64)->nullable()->after('fb_page_url');
            }
            if (! Schema::hasColumn('user_settings', 'fb_user_token')) {
                $table->text('fb_user_token')->nullable()->after('fb_user_id');
            }
        });
    }
};
