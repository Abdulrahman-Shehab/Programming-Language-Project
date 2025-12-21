<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the seeders in the correct order
        $this->call([
            GovernoratesTableSeeder::class,
            CitiesTableSeeder::class,
            UsersTableSeeder::class,
            ApartmentsTableSeeder::class,
        ]);
    }
}
