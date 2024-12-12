<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthenticationController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255'
        ]);

        // Create user
        $user = User::create([
            'username' => $request->input('username'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address')
        ]);

        // Generate access token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return response()->json([
            'message' => 'User Account Created Successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // User login
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Check credentials
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        // Get user and create token
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    // User logout
    public function logout()
    {
        // Revoke all tokens for the authenticated user
        auth()->user()->tokens()->delete();

        // Return response
        return response()->json([
            'message' => 'Successfully Logged out'
        ], 200);
    }
}






// ! ---- old code -----
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;

// class UserAuthenticationController extends Controller
// {
//     public function register(Request $request)
//     {
//         $username = $request->input('username');
//         $email = strtolower($request->input('email'));
//         $password = $request->input('password');
//         $first_name = $request->input('first_name');
//         $last_name = $request->input('last_name');
//         $phone = $request->input('phone');
//         $address = $request->input('address');

//         $user = User::create([
//             'username' => $username,
//             'email' => $email,
//             'password' => Hash::make($password),
//             'first_name' => $first_name,
//             'last_name' => $last_name,
//             'phone' => $phone,
//             'address' => $address
//         ]);

//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'message' => 'User Account Created Successfully',
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//         ], 201);
//     }

//     public function login(Request $request)
//     {
//         $email = strtolower($request->input('email'));
//         $password = $request->input('password');

//         $credentials = [
//             'email' => $email,
//             'password' => $password
//         ];
//         if (!Auth::attempt($credentials)) {
//             return response()->json([
//                 'message' => 'Invalid login credentials'
//             ], 401);
//         }

//         $user = User::where('email', $request['email'])->firstOrFail();

//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'access_token' => $token,
//             'token_type' => 'Bearer',
//         ],200);
//     }
//     public function logout()
//     {
//         auth()->user()->tokens()->delete();

//         return response()->json([
//             'message' => 'Succesfully Logged out'
//         ], 200);
//     }
// }