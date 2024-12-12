<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservationResource;
use App\Models\Reservations;
use App\Models\Hotels;
use App\Models\Rooms;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// for Design Patterns:
use App\Factories\ModelFactory; //(factory Design Patterns)
use App\Models\User;
use App\Singleton\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Stmt\TryCatch;

class ReservationsController extends Controller
{
    public function index()
    {
        return ReservationResource::collection(Reservations::paginate(10));
    }

    public function store(Request $request)
    {
        try{
            $hotel = Hotels::find($request->hotel_id);
            $room = Rooms::find($request->room_id);
    
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required|exists:hotels,id',
                'room_id' => 'required|exists:rooms,id',
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
                'customer_phone' => 'required|string',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'status' => 'required|boolean'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' =>
                $validator->errors()], 400);
            }
    
            // Check if the room in the specified hotel is already reserved during the requested dates
            $existingReservation = Reservations::where('hotel_id', $request->hotel_id)
                ->where('room_id', $request->room_id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                        ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('check_in', '<=', $request->check_in)
                                ->where('check_out', '>=', $request->check_out);
                        });
                })->first();
    
    
            if ($existingReservation) {
                // Return an error message if the room is already reserved during the requested dates
                return response()->json(['error' => 'This room is already reserved for the selected dates.'], 400);
            }
    
            $hotel_id = $hotel->id;
            $room_id = $room->id;
            $user_id = Auth::id();
            $customer_name = $request->input('customer_name');
            $customer_email = $request->input('customer_email');
            $customer_phone = $request->input('customer_phone');
            $check_in = $request->input('check_in');
            $check_out = $request->input('check_out');
            $status = $request->input('status');
    
            // $reservation = Reservations::create([
            //     'hotel_id' => $hotel_id,
            //     'room_id' => $room_id,
            //     'customer_name' => $customer_name,
            //     'customer_email' => $customer_email,
            //     'customer_phone' => $customer_phone,
            //     'check_in' => $check_in,
            //     'check_out' => $check_out,
            //     'status' => $status,
            // ]);
    
            $data = [
                'hotel_id' => $hotel_id,
                'room_id' => $room_id,
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'status' => $status,
            ];
    
            //! using the Singleton Design Pattern
            $logger = Logger::getInstance();
            $logger->log('New reservation created: ' . json_encode($data));
    
    
            //! using the Factory Design Pattern
            $reservation = ModelFactory::create('reservation', [
                'hotel_id' => $hotel_id,
                'room_id' => $room_id,
                'user_id' => $user_id,
                'customer_name' => $customer_name,
                'customer_email' => $customer_email,
                'customer_phone' => $customer_phone,
                'check_in' => $check_in,
                'check_out' => $check_out,
                'status' => "pending",
            ]);
    
    
            return response()->json([
                'data' => new ReservationResource($reservation)
            ], 201);
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating reservation: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create reservation.'], 500);
        }
        
    }

    // public function store(Request $request)
    // {
    //     try {
    //         // Validate incoming data
    //         $validator = Validator::make($request->all(), [
    //             'hotel_id' => 'required|exists:hotels,id',
    //             'room_id' => 'required|exists:rooms,id',
    //             'customer_name' => 'required|string',
    //             'customer_email' => 'required|email',
    //             'customer_phone' => 'required|string',
    //             'check_in' => 'required|date',
    //             'check_out' => 'required|date|after:check_in',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json(['errors' => $validator->errors()], 400);
    //         }

    //         // Create a reservation
    //         $reservation = Reservation::create([
    //             'hotel_id' => $request->hotel_id,
    //             'room_id' => $request->room_id,
    //             'user_id' => Auth::id(),  // Authenticated user's ID
    //             'customer_name' => $request->customer_name,
    //             'customer_email' => $request->customer_email,
    //             'customer_phone' => $request->customer_phone,
    //             'check_in' => $request->check_in,
    //             'check_out' => $request->check_out,
    //             'status' => 'pending', // Default status
    //         ]);

    //         return response()->json([
    //             'message' => 'Reservation created successfully!',
    //             'reservation' => $reservation
    //         ], 201);
    //     } catch (\Exception $e) {
    //         // Log the error for debugging
    //         Log::error('Error creating reservation: ' . $e->getMessage());
    //         return response()->json(['error' => 'Failed to create reservation.'], 500);
    //     }
    // }


    public function show(Reservations $reservation)
    {
        return new ReservationResource($reservation);
    }



    public function update(Request $request, Reservations $reservation)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|numeric',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        //! using the Singleton Design Pattern
        $logger = Logger::getInstance();
        $logger->log('reservation updated: ' . json_encode($reservation));

        // Update the reservation with the new data
        $reservation->update($request->all());

        return response()->json([
            'data' => new ReservationResource($reservation)
        ], 200);
    }

    // todo: when delete retrieve message.

    public function destroy(Reservations $reservation)
    {
        //! using the Singleton Design Pattern
        $logger = Logger::getInstance();
        $logger->log('reservation Deleted: ' . json_encode($reservation));
        
        $reservation->delete();
        
        
        return response()->json(null, 204);
    }
}