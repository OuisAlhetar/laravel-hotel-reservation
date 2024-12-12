<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hotels;
use App\Models\Rooms;
use App\Models\Reservations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $authToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test admin user and log them in
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create authentication token for the admin user
        $this->authToken = $admin->createToken('auth_token')->plainTextToken;
    }

    /** @test */
    public function it_can_display_the_dashboard_data()
    {
        // Create some hotels, rooms, reservations, and users
        $hotel = Hotels::factory()->create();
        $room = Rooms::factory()->create(['hotel_id' => $hotel->id]);
        $reservation = Reservations::factory()->create(['hotel_id' => $hotel->id, 'room_id' => $room->id]);
        $user = User::factory()->create();

        // Make a GET request to the dashboard endpoint
        $response = $this->getJson('/api/v1/dashboard', [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the status code and check the structure of the returned data
        $response->assertStatus(200)
            ->assertJsonStructure([
                'hotels' => ['*' => ['id', 'name', 'description']],
                'rooms' => ['*' => ['id', 'title', 'hotel_id']],
                'reservations' => ['*' => ['id', 'hotel_id', 'room_id']],
                'users' => ['*' => ['id', 'username', 'email']]
            ]);
    }

    /** @test */
    public function it_can_create_a_hotel()
    {
        // Prepare hotel data
        $hotelData = [
            'name' => 'New Hotel',
            'description' => 'A brand new hotel',
            'address' => '123 Main St',
            'city' => 'Sample City',
            'province' => 'Sample Province',
            'country' => 'Sample Country',
            'rating' => 4.52,
            'image_url' => 'https://example.com/image.jpg'
        ];

        // Make a POST request to create a hotel
        $response = $this->postJson('/api/v1/hotels', $hotelData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert that the hotel was created successfully
        $response->assertStatus(201)
            ->assertJsonPath('data.name', $hotelData['name']);

        // Assert that the hotel exists in the database
        $this->assertDatabaseHas('hotels', ['name' => $hotelData['name']]);
    }

    /** @test */
    public function it_can_update_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Prepare updated data
        $updatedData = [
            'name' => 'Updated Hotel Name',
            'description' => 'Updated Description',
            'address' => 'Updated Address',
            'city' => 'Updated City',
            'province' => 'Updated Province',
            'country' => 'Updated Country',
            'rating' => 4.8,
            'image_url' => 'https://example.com/updated-image.jpg'
        ];

        // Make a PUT request to update the hotel
        $response = $this->putJson('/api/v1/hotels/' . $hotel->id, $updatedData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert that the hotel was updated successfully
        $response->assertStatus(200)
            ->assertJsonPath('data.name', $updatedData['name']);

        // Assert that the hotel was updated in the database
        $this->assertDatabaseHas('hotels', ['name' => $updatedData['name']]);
    }

    /** @test */
    public function it_can_delete_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Make a DELETE request to delete the hotel
        $response = $this->deleteJson('/api/v1/hotels/' . $hotel->id, [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the hotel was deleted successfully
        $response->assertStatus(200);

        // Assert the hotel no longer exists in the database
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }

    /** @test */
    public function it_can_create_a_room_for_a_hotel()
    {
        // Create a hotel
        $hotel = Hotels::factory()->create();

        // Prepare room data
        $roomData = [
            'title' => 'Deluxe Room',
            'type' => 'suite',
            'capacity' => 3,
            'facility' => ['WiFi', 'AC'],
            'price' => 200,
            'availability' => true,
            'image_url' => ['https://example.com/room.jpg']
        ];

        // Make a POST request to add a room to the hotel
        $response = $this->postJson('/api/v1/hotels/' . $hotel->id . '/rooms', $roomData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the room was created successfully
        $response->assertStatus(201)
            ->assertJsonPath('room.title', $roomData['title']);

        // Assert the room exists in the database
        $this->assertDatabaseHas('rooms', ['title' => $roomData['title']]);
    }

    /** @test */
    public function it_can_update_a_room()
    {
        // Create a hotel and a room
        $hotel = Hotels::factory()->create();
        $room = Rooms::factory()->create(['hotel_id' => $hotel->id]);

        // Prepare updated room data
        $updatedData = [
            'title' => 'Updated Room Title',
            'type' => 'room',
            'capacity' => 2,
            'facility' => ['WiFi'],
            'price' => 150,
            'availability' => false,
            'image_url' => ['https://example.com/updated-room.jpg']
        ];

        // Make a PUT request to update the room
        $response = $this->putJson('/api/v1/rooms/' . $room->id, $updatedData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the room was updated successfully
        $response->assertStatus(200)
            ->assertJsonPath('room.title', $updatedData['title']);

        // Assert the room was updated in the database
        $this->assertDatabaseHas('rooms', ['title' => $updatedData['title']]);
    }

    /** @test */
    public function it_can_delete_a_room()
    {
        // Create a hotel and a room
        $hotel = Hotels::factory()->create();
        $room = Rooms::factory()->create(['hotel_id' => $hotel->id]);

        // Make a DELETE request to delete the room
        $response = $this->deleteJson('/api/v1/rooms/' . $room->id, [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the room was deleted successfully
        $response->assertStatus(200);

        // Assert the room no longer exists in the database
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    /** @test */
    public function it_can_list_all_users()
    {
        // Create some users
        User::factory()->count(3)->create();

        // Make a GET request to list all users
        $response = $this->getJson('/api/v1/users', [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the users are listed successfully
        $response->assertStatus(200)
            ->assertJsonStructure(['users' => ['*' => ['id', 'username', 'email']]]);
    }

    /** @test */
    public function it_can_update_a_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Prepare updated user data
        $updatedData = [
            'username' => 'UpdatedUsername',
            'email' => 'updatedemail@example.com',
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName',
            'phone' => '123456789',
            'address' => 'Updated Address'
        ];

        // Make a PUT request to update the user
        $response = $this->putJson('/api/v1/users/' . $user->id, $updatedData, [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the user was updated successfully
        $response->assertStatus(200)
            ->assertJsonPath('user.username', $updatedData['username']);

        // Assert the user was updated in the database
        $this->assertDatabaseHas('users', ['username' => $updatedData['username']]);
    }

    /** @test */
    public function it_can_delete_a_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Make a DELETE request to delete the user
        $response = $this->deleteJson('/api/v1/users/' . $user->id, [], [
            'Authorization' => 'Bearer ' . $this->authToken
        ]);

        // Assert the user was deleted successfully
        $response->assertStatus(200);

        // Assert the user no longer exists in the database
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}