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
})->middleware(['auth:sanctum', 'check.user.status']);


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware(['auth:sanctum', 'check.user.status']);

// Governorate and City routes
Route::apiResource('governorates', GovernorateController::class);
Route::apiResource('cities', CityController::class);
Route::get('governorates/{governorate}/cities', [CityController::class, 'index']);

// Apartment routes
Route::get('apartments', [ApartmentController::class, 'index']);
Route::get('my-apartments', [ApartmentController::class, 'myApartments'])->middleware(['auth:sanctum', 'check.user.status']);
Route::apiResource('apartments', ApartmentController::class)->except('index')->middleware(['auth:sanctum', 'check.user.status']);
Route::get('apartments/{id}/check-availability', [ApartmentController::class, 'checkAvailability'])->middleware(['auth:sanctum', 'check.user.status']);

// Booking routes
Route::post('apartments/{apartmentId}/book', [BookingController::class, 'createBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::put('bookings/{id}/modify', [BookingController::class, 'modifyBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::put('bookings/{id}/cancel', [BookingController::class, 'cancelBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::put('bookings/{id}/approve', [BookingController::class, 'confirmBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::put('bookings/{id}/confirm', [BookingController::class, 'confirmBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::put('bookings/{id}/reject', [BookingController::class, 'rejectBooking'])->middleware(['auth:sanctum', 'check.user.status']);
Route::get('bookings', [BookingController::class, 'userBookings'])->middleware(['auth:sanctum', 'check.user.status']);
Route::get('bookings/user', [BookingController::class, 'userBookings'])->middleware(['auth:sanctum', 'check.user.status']);
Route::get('bookings/owner', [BookingController::class, 'ownerBookings'])->middleware(['auth:sanctum', 'check.user.status']);
Route::get('my-bookings', [BookingController::class, 'ownerBookings'])->middleware(['auth:sanctum', 'check.user.status']);
Route::get('apartments/{apartmentId}/bookings', [BookingController::class, 'apartmentBookings'])->middleware(['auth:sanctum', 'check.user.status']);

// Wallet routes
Route::get('wallet', [WalletController::class, 'showBalance'])->middleware(['auth:sanctum', 'check.user.status']);
Route::post('wallet/add-funds', [WalletController::class, 'addFunds'])->middleware(['auth:sanctum', 'check.user.status']);

