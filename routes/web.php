<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PitchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\StaffController;

// ── Marketplace public — pitch search, booking, checkout ──
Route::get('/', [PitchController::class, 'index'])->name('home');
Route::get('/pitches/{pitch:slug}', [PitchController::class, 'show'])->name('pitches.show');

Route::middleware('auth')->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'mine'])->name('bookings.mine');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // Throttled: max 10 booking attempts per minute per user, to stop scripted abuse.
    Route::post('/pitches/{pitch}/book', [BookingController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('bookings.store');

    Route::get('/payments/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/{booking}/checkout/{provider}', [PaymentController::class, 'pay'])->name('payments.pay');

    Route::post('/pitches/{pitch}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Owner dashboard — gated behind the 'owner' role (spatie/laravel-permission).
    // 'admin' always passes too: the platform admin can see and manage every stadium,
    // not just ones they personally own (controllers scope accordingly for each role).
    Route::middleware('role:owner|admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [PitchController::class, 'dashboard'])->name('dashboard');

        Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
        Route::get('/earnings/export', [EarningsController::class, 'export'])->name('earnings.export');

        Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts');
        Route::put('/payouts/destination', [PayoutController::class, 'updateDestination'])->name('payouts.destination');
        Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');

        Route::get('/pitches/create', [PitchController::class, 'create'])->name('pitches.create');
        Route::post('/pitches', [PitchController::class, 'store'])->name('pitches.store');
        Route::get('/pitches/{pitch}/edit', [PitchController::class, 'edit'])->name('pitches.edit');
        Route::put('/pitches/{pitch}', [PitchController::class, 'update'])->name('pitches.update');
        Route::delete('/pitches/{pitch}', [PitchController::class, 'destroy'])->name('pitches.destroy');

        Route::delete('/pitches/{pitch}/images/{image}', [PitchController::class, 'destroyImage'])->name('pitches.images.destroy');
        Route::post('/pitches/{pitch}/images/{image}/primary', [PitchController::class, 'setPrimaryImage'])->name('pitches.images.primary');

        Route::post('/pitches/{pitch}/blocks', [PitchController::class, 'storeBlock'])->name('pitches.blocks.store');
        Route::delete('/pitches/{pitch}/blocks/{block}', [PitchController::class, 'destroyBlock'])->name('pitches.blocks.destroy');
    });

    // Confirming a payout actually moved money is a finance action, distinct from the owner
    // requesting one — kept as its own gate rather than folded into 'owner'.
    Route::middleware('role:finance')->prefix('admin')->name('admin.')->group(function () {
        Route::post('/payouts/{payout}/mark-paid', [PayoutController::class, 'markPaid'])->name('payouts.mark-paid');
        Route::post('/payouts/{payout}/mark-failed', [PayoutController::class, 'markFailed'])->name('payouts.mark-failed');
    });

    // Platform admin only — assigning which staff account is responsible for each stadium.
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/{pitch}/edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{pitch}', [StaffController::class, 'update'])->name('staff.update');
    });
});

// Provider redirects the browser here after checkout — we re-verify server-side, never trust this alone.
Route::get('/payments/callback/{provider}', [PaymentController::class, 'callback'])->name('payments.callback');

// Webhooks are exempt from CSRF (see bootstrap/app.php) but MUST verify the provider's signature.
Route::post('/webhooks/paystack', [PaymentController::class, 'paystackWebhook'])->name('webhooks.paystack');
Route::post('/webhooks/flutterwave', [PaymentController::class, 'flutterwaveWebhook'])->name('webhooks.flutterwave');

// ── Authentification back-office (club) ───────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
// Throttled: 5 attempts/minute per IP+email — this app doesn't use Fortify, so unlike the
// booking/payment routes there's no framework-provided login lockout unless we add it here.
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');

// ── Back-office club — gestion interne (équipes, réservations, factures...) ──
// Réservé au rôle 'admin' : ce périmètre couvre tout le club, pas un stade en particulier,
// et ne doit jamais être accessible à un simple client authentifié.
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Calendrier
    Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier');

    // Équipes
    Route::resource('equipes', EquipeController::class);

    // Réservations
    Route::resource('reservations', ReservationController::class);

    // Paiements
    Route::resource('paiements', PaiementController::class);
    Route::get('/paiements/{paiement}/recu', [PaiementController::class, 'recu'])->name('paiements.recu');

    // Factures
    Route::resource('factures', FactureController::class);
    Route::get('/factures/{facture}/pdf', [FactureController::class, 'pdf'])->name('factures.pdf');

    // Rapports
    Route::get('/rapports', [RapportController::class, 'index'])->name('rapports');

    // Paramètres
    Route::get('/parametres',  [ParametreController::class, 'index'])->name('parametres');
    Route::post('/parametres', [ParametreController::class, 'update'])->name('parametres.update');
    Route::post('/parametres/api-token', [ParametreController::class, 'generateApiToken'])->name('parametres.api-token.generate');
    Route::delete('/parametres/api-token', [ParametreController::class, 'revokeApiToken'])->name('parametres.api-token.revoke');

    // Aide
    Route::get('/aide', [HelpController::class, 'index'])->name('aide');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/lire', [NotificationController::class, 'lire'])->name('notifications.lire');
    Route::post('/notifications/lire-tout', [NotificationController::class, 'lireTout'])->name('notifications.lire-tout');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications-lues', [NotificationController::class, 'destroyLues'])->name('notifications.destroy-lues');
    Route::get('/api/notifications', [NotificationController::class, 'api'])->name('notifications.api');

});
