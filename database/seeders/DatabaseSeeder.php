<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create some users (both admins and regular users)
        \App\Models\User::factory()->count(10)->create(); // Creates 10 users (some admin, some regular)

        // Create some hotels, rooms, and reservations
        \App\Models\Hotels::factory()
            ->count(5) // Create 5 hotels
            ->has(\App\Models\Rooms::factory()->count(10), 'rooms') // Each hotel will have 10 rooms
            ->create();

        // Create reservations for random users
        \App\Models\Reservations::factory()
            ->count(15) // Create 15 reservations
            ->create();
    }
}