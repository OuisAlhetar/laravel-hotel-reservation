<?php

use App\Http\Controllers\UserAuthenticationController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\AdminController; // Add AdminController for admin functionalities
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

######################### Public Routes #########################

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::post('login', [UserAuthenticationController::class, 'login']);
    Route::post('register', [UserAuthenticationController::class, 'register']);
    
    // Publicly available Hotels and Rooms APIs
    Route::apiResource('hotels', HotelsController::class)->only(['index', 'show']);
    Route::apiResource('rooms', RoomsController::class)->only(['index', 'show']);

    // Route to get rooms of a specific hotel
    Route::get('hotels/{hotel}/rooms', [HotelsController::class, 'getRooms']);
});

######################### Private User Routes #########################
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // User Profile routes (regular users)
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::get('/user/reservations', [UserProfileController::class, 'reservations']);
    Route::post('/reservations', [ReservationsController::class, 'store']);
    Route::post('logout', [UserAuthenticationController::class, 'logout']);
});

######################### Admin Routes #########################
Route::prefix('v1')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Admin Dashboard and Management Routes
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Admin-only routes for managing hotels
    Route::post('/hotels', [AdminController::class, 'addHotel']);
    Route::put('/hotels/{hotel}', [AdminController::class, 'updateHotel']);
    Route::delete('/hotels/{hotel}', [AdminController::class, 'deleteHotel']);
    
    // Admin-only routes for managing rooms
    Route::post('/hotels/{hotel}/rooms', [AdminController::class, 'addRoom']);
    Route::put('/rooms/{room}', [AdminController::class, 'updateRoom']);
    Route::delete('/rooms/{room}', [AdminController::class, 'deleteRoom']);
    
    // Admin-only route for canceling reservations
    Route::delete('/reservations/{reservation}', [AdminController::class, 'cancelReservation']);

    // Admin-only routes for managing users
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::put('/users/{user}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
});









// !--- old code -----
// use App\Http\Controllers\UserAuthenticationController;
// use App\Http\Controllers\HotelsController;
// use App\Http\Controllers\RoomsController;
// use App\Http\Controllers\ReservationsController;
// use Illuminate\Support\Facades\Route;

// ######################### Public Routes #########################

// Route::prefix('v1')->group(function () {
//     // Authentication Routes
//     Route::post('login', [UserAuthenticationController::class, 'login']);
//     Route::post('register', [UserAuthenticationController::class, 'register']);
    
//     // Publicly available Hotels and Rooms APIs
//     Route::apiResource('hotels', HotelsController::class)->only(['index', 'show']);
//     Route::apiResource('rooms', RoomsController::class)->only(['index', 'show']);

//     // New route to get rooms of a specific hotel
//     Route::get('hotels/{hotel}/rooms', [HotelsController::class, 'getRooms']);
// });

// ######################### Private Routes #########################
// Route::prefix('v1')->middleware('auth:sanctum','admin')->group(function () {
//     // Logout
//     Route::post('logout', [UserAuthenticationController::class, 'logout']);
    
//     // Admin endpoints for managing hotels, rooms, and reservations
//     Route::apiResource('hotels', HotelsController::class)->except(['index', 'show']);
//     Route::apiResource('rooms', RoomsController::class)->except(['index', 'show']);
//     Route::apiResource('reservations', ReservationsController::class);
//     // Route::get('/dashboard', [AdminController::class, 'dashboard']);
// });