<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\CityController;
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


















