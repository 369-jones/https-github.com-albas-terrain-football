<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\NewBookingOwnerNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Start a payment with the given provider and return the checkout URL to redirect the user to.
     * Card + Mobile Money are both handled by these gateways depending on the country/method the
     * user picks on their hosted checkout page — you don't need separate integrations for each.
     */
    public function initialize(Booking $booking, string $provider): array
    {
        $reference = 'BOOK-'.$booking->id.'-'.Str::random(8);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'provider' => $provider,
            'reference' => $reference,
            'amount' => $booking->total_price,
            'currency' => $booking->currency,
            'status' => 'pending',
        ]);

        return match ($provider) {
            'paystack' => $this->initPaystack($payment),
            'flutterwave' => $this->initFlutterwave($payment),
            default => throw new \InvalidArgumentException("Unsupported provider: $provider"),
        };
    }

    private function initPaystack(Payment $payment): array
    {
        $response = Http::withToken(config('services.paystack.secret'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $payment->user->email,
                'amount' => (int) ($payment->amount * 100), // Paystack expects kobo/cents
                'currency' => $payment->currency,
                'reference' => $payment->reference,
                'callback_url' => route('payments.callback', ['provider' => 'paystack']),
            ])
            ->throw()
            ->json();

        return ['checkout_url' => $response['data']['authorization_url']];
    }

    private function initFlutterwave(Payment $payment): array
    {
        $response = Http::withToken(config('services.flutterwave.secret'))
            ->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $payment->reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'redirect_url' => route('payments.callback', ['provider' => 'flutterwave']),
                'customer' => ['email' => $payment->user->email],
            ])
            ->throw()
            ->json();

        return ['checkout_url' => $response['data']['link']];
    }

    /**
     * Verify a transaction server-side after redirect/webhook. NEVER trust the client-side
     * redirect alone to mark a booking as paid — always re-verify with the provider's API.
     */
    public function verify(Payment $payment): bool
    {
        $success = match ($payment->provider) {
            'paystack' => $this->verifyPaystack($payment),
            'flutterwave' => $this->verifyFlutterwave($payment),
            default => false,
        };

        if ($success) {
            $payment->update(['status' => 'successful', 'paid_at' => now()]);
            $payment->booking->update(['status' => 'confirmed', 'payment_status' => 'paid']);

            $booking = $payment->booking->fresh(['pitch.owner', 'user']);
            $booking->user->notify(new BookingConfirmedNotification($booking));
            $booking->pitch->owner?->notify(new NewBookingOwnerNotification($booking));
        } else {
            $payment->update(['status' => 'failed']);
        }

        return $success;
    }

    private function verifyPaystack(Payment $payment): bool
    {
        $response = Http::withToken(config('services.paystack.secret'))
            ->get("https://api.paystack.co/transaction/verify/{$payment->reference}")
            ->json();

        $payment->update(['raw_response' => $response]);

        return ($response['data']['status'] ?? null) === 'success';
    }

    private function verifyFlutterwave(Payment $payment): bool
    {
        $response = Http::withToken(config('services.flutterwave.secret'))
            ->get('https://api.flutterwave.com/v3/transactions/verify_by_reference', [
                'tx_ref' => $payment->reference,
            ])
            ->json();

        $payment->update(['raw_response' => $response]);

        return ($response['data']['status'] ?? null) === 'successful';
    }
}
