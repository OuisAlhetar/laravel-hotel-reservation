<?php

namespace Tests\Feature;

use App\Models\Hotels;
use App\Models\User; // Import the User model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HotelControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and log them in
        $user = User::factory()->create();

        // Assuming you're using Sanctum for authentication
        $this->authToken = $user->createToken('auth_token')->plainTextToken;
    }

    /** @test */
    public function it_can_list_hotels()
    {
        // Create 3 hotels
        Hotels::factory()->count(3)->create();

        // Make a GET request to the hotel index route
        $response = $this->getJson('/api/v1/hotels', [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Check that the status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains 3 hotels
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_create_a_hotel()
    {
        // Hotel data
        $hotelData = [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'country' => $this->faker->country,
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'image_url' => $this->faker->imageUrl()
        ];

        // Make a POST request to the hotel store route
        $response = $this->postJson('/api/v1/hotels', $hotelData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Check that the status is 201 (created)
        $response->assertStatus(201);

        // Assert that the hotel was created in the database
        $this->assertDatabaseHas('hotels', ['name' => $hotelData['name']]);
    }

    /** @test */
    public function it_can_show_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Make a GET request to the hotel show route
        $response = $this->getJson('/api/v1/hotels/' . $hotel->id, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Check that the status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the hotel name is in the response
        $response->assertJsonPath('data.name', $hotel->name);
    }

    /** @test */
    public function it_can_update_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Updated data
        $updatedData = [
            'name' => 'Updated Hotel Name',
            'description' => 'Updated Description',
            'address' => 'Updated Address',
            'city' => 'Updated City',
            'province' => 'Updated Province',
            'country' => 'Updated Country',
            'rating' => 4.5,
            'image_url' => 'https://example.com/updated-image.jpg'
        ];

        // Make a PUT request to the hotel update route
        $response = $this->putJson('/api/v1/hotels/' . $hotel->id, $updatedData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Check that the status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the hotel was updated in the database
        $this->assertDatabaseHas('hotels', ['name' => 'Updated Hotel Name']);
    }

    /** @test */
    public function it_can_delete_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Make a DELETE request to the hotel destroy route
        $response = $this->deleteJson('/api/v1/hotels/' . $hotel->id, [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Check that the status is 204 (No Content)
        $response->assertStatus(200);

        // Assert that the hotel was deleted from the database
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}