<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('page_url', 500)->unique();
            $table->timestamps();
        });

        // Migrate existing data from user_settings to user_facebook_pages
        $users = DB::table('user_settings')
            ->whereNotNull('fb_page_url')
            ->where('fb_page_url', '!=', '')
            ->get();

        $inserted = 0;
        foreach ($users as $setting) {
            try {
                DB::table('user_facebook_pages')->insert([
                    'user_id' => $setting->user_id,
                    'page_url' => $setting->fb_page_url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            } catch (Throwable $e) {
                // Skip duplicates silently
            }
        }

        if ($inserted > 0) {
            echo "Migrated {$inserted} Facebook page(s) to user_facebook_pages.\n";
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_facebook_pages');
    }
};
