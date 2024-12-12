<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelsFactory extends Factory
{
    protected $model = \App\Models\Hotels::class;

    public function definition()
    {
        return [
            'name' => $this->faker->lexify('Hotel ?????'),
            'description' => $this->faker->sentence(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'province' => $this->faker->state(),
            'country' => $this->faker->word(),
            'rating' => $this->faker->randomFloat(2, 3, 5), // Random rating between 3 and 5
            'image_url' => $this->faker->imageUrl(640, 480, 'hotels'), // Fake image
        ];
    }
}