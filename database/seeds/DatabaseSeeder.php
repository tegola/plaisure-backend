<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Non-destructive seeders
        $this->call([
        	AmenitiesTableSeeder::class,
        	VenueCategoriesTableSeeder::class
        ]);
    }
}
