<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition()
    {
        return [
            'username' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify(9),
            'first_name'=> $this->faker->name(),
            'last_name'=> $this->faker->name(),
            'address' => $this->faker->address(),
            'password' => Hash::make('password'), // Default password
            'role' => $this->faker->randomElement(['user', 'admin']), // Assign random role
        ];
    }
}