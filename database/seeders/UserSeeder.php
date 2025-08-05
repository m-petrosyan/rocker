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
        if (app()->environment('local')) {
            $user = User::query()->firstOrCreate(
                ['email' => 'admin@gmail.com'],
                [
                    'name' => 'admin',
                    'username' => 'admin',
                    'password' => bcrypt('12345678'),
                    'email_verified_at' => now(),
                ]
            );

            $user->assignRole('admin');
        }
    }
}
