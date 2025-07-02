<?php

use App\Enums\CountyEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('event_id');
            $table->morphs('userable');
            $table->string('title');
            $table->text('content');
            $table->text('link')->nullable();
            $table->text('ticket')->nullable();
            $table->string('price')->nullable();
            $table->string('country')->default(CountyEnum::ARMENIA->value)->index();
            $table->string('city')->index()->default('all')->after('country');
            $table->string('location');
            $table->json('cordinates')->nullable();
            $table->smallInteger('type')->unsigned()->default(EventTypeEnum::CONCERTS_EVENTS->value);
            $table->date('start_date')->index();
            $table->time('start_time')->nullable();
            $table->integer('notify_count')->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
