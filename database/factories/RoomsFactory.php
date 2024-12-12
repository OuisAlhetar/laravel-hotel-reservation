<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomsFactory extends Factory
{
    protected $model = \App\Models\Rooms::class;

    public function definition()
    {
        return [
            'hotel_id' => \App\Models\Hotels::factory(), // Associate the room with a hotel
            'title' => $this->faker->lexify('Room ?????'),
            'type' => $this->faker->randomElement(['Single', 'Double', 'Suite']),
            'capacity' => $this->faker->numberBetween(1, 5),
            'facility'=> $this->faker->randomElement(['wifi','wide','saona','double-bed','bool','wife']),
            'price' => $this->faker->numberBetween(100, 500),
            'availability' => $this->faker->boolean(),
            'image_url' => $this->faker->imageUrl(640, 480, 'rooms'), // Fake image
        ];
    }
}