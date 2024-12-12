<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationsFactory extends Factory
{
    protected $model = \App\Models\Reservations::class;

    public function definition()
    {
        return [
            'hotel_id' => \App\Models\Hotels::factory(), // Associate with a hotel
            'room_id' => \App\Models\Rooms::factory(), // Associate with a room
            'user_id' => \App\Models\User::factory(), // Associate with a user
            'customer_name' => $this->faker->name(),
            'customer_email'=> $this->faker->email(),
            'customer_phone' => $this->faker->numerify(),
            'check_in' => $this->faker->dateTimeBetween('+0 days', '+1 month'),
            'check_out' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => $this->faker->randomElement(['confirmed', 'pending', 'cancelled']),
        ];
    }
}