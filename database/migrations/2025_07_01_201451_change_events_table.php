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
            $table->dropColumn('event_id');
            $table->string('title')->after('id');
            $table->text('content')->after('title');
            $table->text('link')->nullable();
            $table->text('ticket')->nullable();
            $table->string('price')->nullable();
            $table->string('country')->default(CountyEnum::ARMENIA->value)->after('content')->index();
            $table->string('city')->index()->default('all')->after('country');
            $table->string('genre')->nullable()->index();
            $table->string('location');
            $table->json('cordinates')->nullable();
            $table->smallInteger('type')->unsigned()->default(1);
            $table->date('start_date')->nullable()->index();
            $table->time('start_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'title',
                    'content',
                    'link',
                    'ticket',
                    'price',
                    'city',
                    'cordinates',
                    'notify_count',
                    'genre',
                    'location',
                    'country',
                ]
            );
            $table->dropColumn('type');
            $table->dropColumn('start_date');
            $table->dropColumn('start_time');
        });
    }
};
