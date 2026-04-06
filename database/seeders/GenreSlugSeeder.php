<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::whereNull('slug')->orWhere('slug', '')->get()->each(function (Genre $genre) {
            $genre->update(['slug' => Str::slug($genre->name)]);
        });
        
        $this->command->info('Slugs updated for missing genres.');
    }
}
