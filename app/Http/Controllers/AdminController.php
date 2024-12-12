<?php

namespace App\Http\Controllers;

use App\Factories\ModelFactory;
use App\Http\Resources\HotelResource;
use App\Http\Resources\RoomResource;
use App\Models\Hotels;
use App\Models\Reservations;
use App\Models\Rooms;
use App\Models\User;
use App\Singleton\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    // Admin dashboard overview
    public function dashboard()
    {
        // Fetch all hotels, rooms, reservations, and users
        $hotels = Hotels::all();
        $rooms = Rooms::all();
        $reservations = Reservations::all();
        $users = User::all();

        return response()->json([
            'hotels' => $hotels,
            'rooms' => $rooms,
            'reservations' => $reservations,
            'users' => $users,
        ]);
    }

    // Add a new hotel
    public function addHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'country' => 'required|string',
            'rating' => 'required|decimal:2',
            'image_url' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' =>
            $validator->errors()], 400);
        }

        $name = $request->input('name');
        $description = $request->input('description');
        $address = $request->input('address');
        $city = $request->input('city');
        $province = $request->input('province');
        $country = $request->input('country');
        $rating = $request->input('rating');
        $image_url = $request->input('image_url');

        $data = [
            'name' => $name,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'province' => $province,
            'country' => $country,
            'rating' => $rating,
            'image_url' => $image_url,
        ];

        //! using the Singleton Design Pattern
        $logger = Logger::getInstance();
        $logger->log('New Hotel created: ' . json_encode($data));

        //! using the Factory Design Pattern
        $hotel = ModelFactory::create('hotel', $data);


        return response()->json([
            'data' => new HotelResource($hotel)
        ], 201);
    }

    // Update a hotel
    public function updateHotel(Request $request, Hotels $hotel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'country' => 'required|string',
            'rating' => 'required|numeric|min:0|max:5',
            'image_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $hotel->update($request->all());

        return response()->json([
            'data' => new HotelResource($hotel)
        ], 200);
    }

    // Delete a hotel
    public function deleteHotel(Hotels $hotel)
    {
        $hotel->delete();
        return response()->json(['message' => 'Hotel deleted successfully'], 200);
    }

    // Add a room to a hotel
    public function addRoom(Request $request, $hotelId)
    {
        $hotel = Hotels::findOrFail($hotelId);
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type' => 'required|string',
            'capacity' => 'required|integer',
            'facility' => 'required|array',
            'price' => 'required|integer',
            'availability' => 'required|boolean',
            'image_url' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' =>
            $validator->errors()], 400);
        }

        $hotel_id = $hotel->id;
        $title = $request->input('title');
        $type = $request->input('type');
        $capacity = $request->input('capacity');
        $facility = $request->input('facility');
        $price = $request->input('price');
        $availability = $request->input('availability');
        $image_url = $request->input('image_url');

        $data = [
            'hotel_id' => $hotelId,
            'title' => $title,
            'type' => $type,
            'capacity' => $capacity,
            'facility' => $facility,
            'price' => $price,
            'availability' => $availability,
            'image_url' => $image_url,
        ];

        //! using the Singleton Design Pattern
        $logger = Logger::getInstance();
        $logger->log('New room created: ' . json_encode($data));


        //! using the Factory Design Pattern
        $room = ModelFactory::create('room', [
            'hotel_id' => $hotel_id,
            'title' => $title,
            'type' => $type,
            'capacity' => $capacity,
            'facility' => $facility,
            'price' => $price,
            'availability' => $availability,
            'image_url' => $image_url,
        ]);

        return response()->json(['message' => 'Room added successfully', 'room' => new RoomResource($room)], 201);
    }

    // Update a room
    public function updateRoom(Request $request, $roomId)
    {
        $room = Rooms::findOrFail($roomId);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'type' => 'required|string',
            'capacity' => 'required|integer',
            'facility' => 'required|array',
            'price' => 'required|integer',
            'availability' => 'required|boolean',
            'image_url' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $room->update($request->all());

        return response()->json(['message' => 'Room updated successfully', 'room' => new RoomResource($room)], 200);
    }

    // Delete a room
    public function deleteRoom(Rooms $room)
    {
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully'], 200);
    }

    // Cancel a reservation
    public function cancelReservation(Reservations $reservation)
    {
        $reservation->delete();

        return response()->json(['message' => 'Reservation canceled successfully'], 200);
    }

    // List all users
    public function listUsers()
    {
        
        $users = User::all();

        return response()->json(['users' => $users], 200);
    }

    // Update a user
    public function updateUser(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validator = Validator::make(
            $request->all(),[
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        
        $user->update($request->all());

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    // Delete a user
    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}