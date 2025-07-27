<?php

use App\Enums\EventTypeEnum;
use App\Enums\LanguageEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('country')->nullable()->index();
            $table->string('city')->nullable();
            $table->string('language_code')->default(LanguageEnum::ENGLISH->value);
            $table->string('genre')->nullable();
            $table->boolean('events_notifications')->default(true);
            $table->smallInteger('events_concerts')->unsigned()->default(EventTypeEnum::CONCERTS_EVENTS->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
