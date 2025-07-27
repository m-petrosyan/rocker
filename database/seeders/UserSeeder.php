<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'john@gmail.com'],
            [
                'email' => 'john@gmail.com',
                'email_verified_at' => now(),
                'name' => 'John doe',
                'username' => 'john',
                'password' => '12345678',
            ]
        )->assignRole('admin');
    }
}
