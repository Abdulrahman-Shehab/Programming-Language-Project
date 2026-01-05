<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // User management
        Route::get('/users/pending', [AdminController::class, 'showPendingUsers'])->name('users.pending');
        Route::get('/users/approved', [AdminController::class, 'showApprovedUsers'])->name('users.approved');
        Route::get('/users/rejected', [AdminController::class, 'showRejectedUsers'])->name('users.rejected');
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser']);
        Route::post('/users/{id}/reject', [AdminController::class, 'rejectUser']);
        Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser']);
        Route::post('/users/{id}/re-approve', [AdminController::class, 'reApproveUser']);
        Route::post('/users/{userId}/add-funds', [AdminController::class, 'addFunds']);

        // Apartment management
        Route::get('/apartments', [AdminController::class, 'showApartments'])->name('apartments');
        Route::delete('/apartments/{id}/delete', [AdminController::class, 'deleteApartment']);

        // Location management
        Route::get('/locations', [AdminController::class, 'showLocations'])->name('locations');
        Route::post('/governorates', [AdminController::class, 'addGovernorate']);
        Route::delete('/governorates/{id}/delete', [AdminController::class, 'deleteGovernorate']);
        Route::post('/cities', [AdminController::class, 'addCity']);
        Route::delete('/cities/{id}/delete', [AdminController::class, 'deleteCity']);
    });
});


