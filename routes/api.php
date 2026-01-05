<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Governorate and City routes
Route::apiResource('governorates', GovernorateController::class);
Route::apiResource('cities', CityController::class);
Route::get('governorates/{governorate}/cities', [CityController::class, 'index']);

// Apartment routes
Route::get('apartments', [ApartmentController::class, 'index']);
Route::apiResource('apartments', ApartmentController::class)->except('index')->middleware('auth:sanctum');

// Booking routes
Route::post('apartments/{apartmentId}/book', [BookingController::class, 'createBooking'])->middleware('auth:sanctum');
Route::put('bookings/{id}/modify', [BookingController::class, 'modifyBooking'])->middleware('auth:sanctum');
Route::put('bookings/{id}/cancel', [BookingController::class, 'cancelBooking'])->middleware('auth:sanctum');
Route::put('bookings/{id}/confirm', [BookingController::class, 'confirmBooking'])->middleware('auth:sanctum');
Route::put('bookings/{id}/reject', [BookingController::class, 'rejectBooking'])->middleware('auth:sanctum');
Route::get('bookings', [BookingController::class, 'userBookings'])->middleware('auth:sanctum');
Route::get('my-bookings', [BookingController::class, 'ownerBookings'])->middleware('auth:sanctum');
Route::get('apartments/{apartmentId}/bookings', [BookingController::class, 'apartmentBookings'])->middleware('auth:sanctum');

// Wallet routes
Route::get('wallet', [WalletController::class, 'showBalance'])->middleware('auth:sanctum');
Route::post('wallet/add-funds', [WalletController::class, 'addFunds'])->middleware('auth:sanctum');

