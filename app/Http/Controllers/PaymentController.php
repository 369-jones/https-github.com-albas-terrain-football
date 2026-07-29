<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function checkout(Booking $booking): View
    {
        $this->authorizeOwnerOf($booking);

        return view('payments.checkout', compact('booking'));
    }

    public function pay(Booking $booking, string $provider): RedirectResponse
    {
        $this->authorizeOwnerOf($booking);

        $result = $this->payments->initialize($booking, $provider);

        return redirect()->away($result['checkout_url']);
    }

    /**
     * The user is redirected here by the provider after paying. We treat this only
     * as a hint to re-check — the actual confirmation comes from server-side verify()
     * below, never from the redirect itself (a user could forge this URL).
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        $reference = $request->query('reference') ?? $request->query('tx_ref');
        $payment = Payment::where('reference', $reference)->where('provider', $provider)->firstOrFail();

        $this->authorizeOwnerOf($payment->booking);

        $success = $this->payments->verify($payment);

        return $success
            ? redirect()->route('bookings.show', $payment->booking)->with('success', __('Payment confirmed — your pitch is booked!'))
            : redirect()->route('payments.checkout', $payment->booking)->with('error', __('Payment was not successful. Please try again.'));
    }

    /**
     * Paystack webhook — verify the X-Paystack-Signature HMAC before trusting the payload.
     * Configure this URL in your Paystack dashboard.
     */
    public function paystackWebhook(Request $request): Response
    {
        $signature = $request->header('X-Paystack-Signature');
        $expected = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret'));

        if (! $signature || ! hash_equals($expected, $signature)) {
            Log::warning('Rejected Paystack webhook with invalid signature.');
            return response('Invalid signature', 401);
        }

        $event = $request->input('event');
        if ($event === 'charge.success') {
            $reference = $request->input('data.reference');
            if ($payment = Payment::where('reference', $reference)->first()) {
                $this->payments->verify($payment);
            }
        }

        return response('ok');
    }

    /**
     * Flutterwave webhook — verify the verif-hash header before trusting the payload.
     * Configure this URL + the same secret hash in your Flutterwave dashboard.
     */
    public function flutterwaveWebhook(Request $request): Response
    {
        $signature = $request->header('verif-hash');

        if (! $signature || ! hash_equals(config('services.flutterwave.webhook_hash'), $signature)) {
            Log::warning('Rejected Flutterwave webhook with invalid signature.');
            return response('Invalid signature', 401);
        }

        $reference = $request->input('data.tx_ref');
        if ($reference && $payment = Payment::where('reference', $reference)->first()) {
            $this->payments->verify($payment);
        }

        return response('ok');
    }

    private function authorizeOwnerOf(Booking $booking): void
    {
        abort_unless($booking->user_id === request()->user()?->id, 403);
    }
}
