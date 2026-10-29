<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Admin users are created with `php artisan make:filament-user`.
     */
    public function run(): void
    {
        Setting::query()->firstOrCreate([], [
            'name' => 'Naiyem',
            'title' => 'Full-stack developer',
            'seo_title' => 'Leo Naiyem | Full-stack developer',
        ]);
    }
}
