<?php

namespace App\Http\Controllers;

use App\Http\Resources\HotelResource;
use App\Models\Hotels;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// for Design Patterns:
use App\Factories\ModelFactory; //(factory Design Patterns)
use App\Http\Resources\RoomResource;
use App\Singleton\Logger; //(Singleton Design Patterns)

class HotelsController extends Controller
{
    public function index()
    {
        $result = HotelResource::collection(Hotels::paginate(5));

        return $result;
    }

    public function store(Request $request)
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


        // $hotel = Hotels::create([
        //     'name' => $name,
        //     'description' => $description,
        //     'address' => $address,
        //     'city' => $city,
        //     'province' => $province,
        //     'country' => $country,
        //     'rating' => $rating,
        //     'image_url' => $image_url,
        // ]);

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
        ], 200);
    }

    public function show(Hotels $hotel)
    {
        return new HotelResource($hotel);
    }

    //! --- old -
    // public function update(Request $request, Hotels $hotel)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string',
    //         'description' => 'required|string',
    //         'address' => 'required|string',
    //         'city' => 'required|string',
    //         'province' => 'required|string',
    //         'country' => 'required|string',
    //         'rating' => 'required|decimal:2',
    //         'image_url' => 'required|string'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['errors' =>
    //         $validator->errors()], 400);
    //     }

    //     $name = $request->input('name');
    //     $description = $request->input('description');
    //     $address = $request->input('address');
    //     $city = $request->input('city');
    //     $province = $request->input('province');
    //     $country = $request->input('country');
    //     $rating = $request->input('rating');
    //     $image_url = $request->input('image_url');

    //     $hotel = Hotels::create([
    //         'name' => $name,
    //         'description' => $description,
    //         'address' => $address,
    //         'city' => $city,
    //         'province' => $province,
    //         'country' => $country,
    //         'rating' => $rating,
    //         'image_url' => $image_url,
    //     ]);
    //         return response()->json([
    //             'data' => new HotelResource($hotel)
    //         ], 200);
    // }

    public function update(Request $request, Hotels $hotel)
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

    public function getRooms($hotel_id)
    {
        // Find the hotel
        $hotel = Hotels::findOrFail($hotel_id);

        // Get all rooms for the hotel
        $rooms = $hotel->rooms; // Assuming the Hotel model has a relationship with Rooms

        // Return rooms as a resource collection
        return RoomResource::collection($rooms);
    }


    public function destroy(Hotels $hotel)
    {
        $hotel->delete();
        return response()->json(["message:" => "Hotel Deleted"], 200);
    }
}