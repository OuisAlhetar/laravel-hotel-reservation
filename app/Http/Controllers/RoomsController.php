<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Rooms;
use App\Models\Hotels;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// for Design Patterns:
use App\Factories\ModelFactory; //(factory Design Patterns)
use App\Singleton\Logger;

class RoomsController extends Controller
{
    public function index()
    {

        return RoomResource::collection(Rooms::paginate(5));
    }

    public function store(Request $request)
    {
        $hotel = Hotels::find($request->hotel_id);
        $facility = [];
        $image_url = [];

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

        // $room = Rooms::create([
        //     'hotel_id' => $hotel_id,
        //     'title' => $title,
        //     'type' => $type,
        //     'capacity' => $capacity,
        //     'facility' => $facility,
        //     'price' => $price,
        //     'availability' => $availability,
        //     'image_url' => $image_url,
        // ]);

        $data = [
            'hotel_id' => $hotel_id,
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
        $room = ModelFactory::create('room',[
            'hotel_id' => $hotel_id,
            'title' => $title,
            'type' => $type,
            'capacity' => $capacity,
            'facility' => $facility,
            'price' => $price,
            'availability' => $availability,
            'image_url' => $image_url,
        ]);
        
        return response()->json([
            'data' => new RoomResource($room)
        ], 201);
    }

    public function show(Rooms $room)
    {
        return new RoomResource($room);
    }

    // ! --- old -----
    // public function update(Request $request, Rooms $room)
    // {
    //     $hotel = Hotels::find($request->hotel_id);
    //     $facility = [];
    //     $image_url = [];

    //     $validator = Validator::make($request->all(), [
    //         'title' => 'required|string',
    //         'type' => 'required|string',
    //         'capacity' => 'required|integer',
    //         'facility' => 'required|array',
    //         'price' => 'required|integer',
    //         'availability' => 'required|boolean',
    //         'image_url' => 'required|array'
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['errors' =>
    //         $validator->errors()], 400);
    //     }

    //     $hotel_id = $hotel->id;
    //     $title = $request->input('title');
    //     $type = $request->input('type');
    //     $capacity = $request->input('capacity');
    //     $facility = $request->input('facility');
    //     $price = $request->input('price');
    //     $availability = $request->input('availability');
    //     $image_url = $request->input('image_url');

    //     $room = Rooms::create([
    //         'hotel_id' => $hotel_id,
    //         'title' => $title,
    //         'type' => $type,
    //         'capacity' => $capacity,
    //         'facility' => $facility,
    //         'price' => $price,
    //         'availability' => $availability,
    //         'image_url' => $image_url,
    //     ]);
    //         return response()->json([
    //             'data' => new RoomResource($room)
    //         ], 200);
    // }

    public function update(Request $request, Rooms $room)
    {
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

        return response()->json([
            'data' => new RoomResource($room)
        ], 200);
    }


    public function destroy(Rooms $room)
    {
        $room->delete();
        return response()->json(null, 204);
    }
}