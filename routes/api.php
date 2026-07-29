<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PitchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Bearer-token API, authenticated with the personal access tokens issued from
// Parametres > Cle API (see StaffController/ParametreController on the web side).
// Read-only for now — scoped the same way the owner dashboard is: the platform
// admin sees every stadium, everyone else only the one(s) they're responsible for.
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user()->only('id', 'name', 'email');
    })->name('me');

    Route::get('/pitches', [PitchController::class, 'index'])->name('pitches.index');
    Route::get('/pitches/{pitch}', [PitchController::class, 'show'])->name('pitches.show');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
});
