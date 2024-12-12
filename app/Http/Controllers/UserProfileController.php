<?php

namespace App\Http\Controllers;

use App\Models\Reservations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    // Show user profile information
    public function show()
    {
        $user = Auth::user(); // Get the currently authenticated user

        return response()->json([
            'data' => [
                'name' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role'=> $user->role,
            ]
        ]);
    }

    // Show user-specific reservations
    public function reservations()
    {
        $user = Auth::user(); // Get the currently authenticated user

        // Assuming the 'Reservation' model has a 'user_id' column that links to the user
        $reservations = Reservations::where('user_id', $user->id)->get();

        return response()->json([
            'data' => $reservations
        ]);
    }
}