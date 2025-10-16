<?php

namespace Database\Seeders;

use App\Models\Bot;
use Illuminate\Database\Seeder;

class TelegraphBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bot::query()->firstOrCreate(
            ['name' => 'RockerBotDev'],
            [
                'token' => env('TELEGRAPH_BOT_TOKEN', '7025055899:AAHF-aCp5_LnBXqANcq6tUuFrMky3IvU2VI'),
            ]
        );
    }
}
