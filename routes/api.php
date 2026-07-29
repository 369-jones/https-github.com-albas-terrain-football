<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EquipeController;
use App\Http\Controllers\Api\PayoutController;
use App\Http\Controllers\Api\PitchController;
use App\Http\Controllers\Api\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Bearer-token API, authenticated with the personal access tokens issued from
// Parametres > Cle API (see StaffController/ParametreController on the web side).
// Scoped the same way the owner dashboard is: the platform admin sees and manages
// every stadium, everyone else only the one(s) they're responsible for.
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user()->only('id', 'name', 'email');
    })->name('me');

    Route::get('/pitches', [PitchController::class, 'index'])->name('pitches.index');
    Route::get('/pitches/{pitch}', [PitchController::class, 'show'])->name('pitches.show');
    Route::post('/pitches', [PitchController::class, 'store'])->name('pitches.store');
    // PUT, not PATCH: validatePitch() requires the full set of fields, same as the
    // web edit form — this replaces the pitch's details rather than patching a subset.
    Route::put('/pitches/{pitch}', [PitchController::class, 'update'])->name('pitches.update');
    Route::delete('/pitches/{pitch}', [PitchController::class, 'destroy'])->name('pitches.destroy');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');

    // Payouts are always self-scoped (index/destination/store act on the caller's own
    // balance) except mark-paid/mark-failed, which require the 'finance' role and can
    // act on any owner's payout — matching the web PayoutController exactly.
    Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
    Route::put('/payouts/destination', [PayoutController::class, 'updateDestination'])->name('payouts.destination');
    Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
    Route::post('/payouts/{payout}/mark-paid', [PayoutController::class, 'markPaid'])->name('payouts.mark-paid');
    Route::post('/payouts/{payout}/mark-failed', [PayoutController::class, 'markFailed'])->name('payouts.mark-failed');
});

// Legacy club back-office (Equipes/Reservations) — a single club, not per-stadium, so this
// is gated to role:admin only, exactly like its web equivalent (routes/web.php's
// 'auth','role:admin' group), not the owner|admin scoping used above.
Route::middleware(['auth:sanctum', 'throttle:api', 'role:admin'])->prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('equipes', EquipeController::class);
    Route::apiResource('reservations', ReservationController::class);
});
