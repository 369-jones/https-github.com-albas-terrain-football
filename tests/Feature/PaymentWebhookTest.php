<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.paystack.secret' => 'paystack-test-secret',
            'services.flutterwave.webhook_hash' => 'flutterwave-test-hash',
        ]);

        // PaymentService::verify() always re-checks with the provider's live API rather
        // than trusting the webhook payload alone — fake that call instead of hitting
        // the real network in tests.
        Http::preventStrayRequests();
    }

    private function pendingPayment(string $provider): Payment
    {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        return Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'provider' => $provider,
            'reference' => 'BOOK-'.$booking->id.'-TEST',
            'amount' => $booking->total_price,
            'currency' => $booking->currency,
            'status' => 'pending',
        ]);
    }

    public function test_paystack_webhook_confirms_booking_with_a_valid_signature(): void
    {
        Http::fake(['api.paystack.co/*' => Http::response(['data' => ['status' => 'success']])]);

        $payment = $this->pendingPayment('paystack');

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => $payment->reference],
        ]);
        $signature = hash_hmac('sha512', $payload, 'paystack-test-secret');

        $response = $this->call('POST', '/webhooks/paystack', [], [], [], [
            'HTTP_X-Paystack-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertOk();
        $this->assertSame('successful', $payment->fresh()->status);
        $this->assertSame('confirmed', $payment->booking->fresh()->status);
        $this->assertSame('paid', $payment->booking->fresh()->payment_status);
    }

    public function test_paystack_webhook_rejects_an_invalid_signature(): void
    {
        Http::fake(); // signature check must fail before any provider call is attempted

        $payment = $this->pendingPayment('paystack');

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => $payment->reference],
        ]);

        $response = $this->call('POST', '/webhooks/paystack', [], [], [], [
            'HTTP_X-Paystack-Signature' => 'not-the-real-signature',
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertStatus(401);
        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertSame('pending', $payment->booking->fresh()->status);
    }

    public function test_paystack_webhook_rejects_a_missing_signature(): void
    {
        Http::fake();

        $payment = $this->pendingPayment('paystack');

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => $payment->reference],
        ]);

        $response = $this->call('POST', '/webhooks/paystack', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertStatus(401);
        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_flutterwave_webhook_confirms_booking_with_a_valid_hash(): void
    {
        Http::fake(['api.flutterwave.com/*' => Http::response(['data' => ['status' => 'successful']])]);

        $payment = $this->pendingPayment('flutterwave');

        $response = $this->call('POST', '/webhooks/flutterwave', [], [], [], [
            'HTTP_verif-hash' => 'flutterwave-test-hash',
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'data' => ['tx_ref' => $payment->reference],
        ]));

        $response->assertOk();
        $this->assertSame('successful', $payment->fresh()->status);
        $this->assertSame('confirmed', $payment->booking->fresh()->status);
    }

    public function test_flutterwave_webhook_rejects_an_invalid_hash(): void
    {
        Http::fake();

        $payment = $this->pendingPayment('flutterwave');

        $response = $this->call('POST', '/webhooks/flutterwave', [], [], [], [
            'HTTP_verif-hash' => 'wrong-hash',
        ], json_encode([
            'data' => ['tx_ref' => $payment->reference],
        ]));

        $response->assertStatus(401);
        $this->assertSame('pending', $payment->fresh()->status);
    }
}
