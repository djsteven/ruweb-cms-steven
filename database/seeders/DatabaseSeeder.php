<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocaleSeeder::class,
            AdminUserSeeder::class,
            SettingsSeeder::class,
            HomepageSeeder::class,
            MenuSeeder::class,
            GoogleReviewsSettingsSeeder::class,
        ]);
    }
}
