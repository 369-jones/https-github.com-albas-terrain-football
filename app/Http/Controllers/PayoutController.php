<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Payout;
use App\Models\PayoutItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        $owner = $request->user();

        $available = $this->availableBalanceQuery($owner->id)
            ->get()
            ->groupBy('currency')
            ->map(fn ($payments) => $payments->sum('amount'));

        $payouts = Payout::where('owner_id', $owner->id)
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payouts.index', compact('owner', 'available', 'payouts'));
    }

    public function updateDestination(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payout_method' => ['required', 'in:bank_transfer,mobile_money'],
            'bank_name' => ['required_if:payout_method,bank_transfer', 'nullable', 'string', 'max:120'],
            'account_name' => ['required_if:payout_method,bank_transfer', 'nullable', 'string', 'max:120'],
            'account_number' => ['required_if:payout_method,bank_transfer', 'nullable', 'string', 'max:60'],
            'mobile_provider' => ['required_if:payout_method,mobile_money', 'nullable', 'string', 'max:60'],
            'mobile_number' => ['required_if:payout_method,mobile_money', 'nullable', 'string', 'max:30'],
        ]);

        $details = $validated['payout_method'] === 'bank_transfer'
            ? [
                'bank_name' => $validated['bank_name'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
            ]
            : [
                'provider' => $validated['mobile_provider'],
                'number' => $validated['mobile_number'],
            ];

        $request->user()->update([
            'payout_method' => $validated['payout_method'],
            'payout_details' => $details,
        ]);

        return back()->with('success', __('Payout destination updated.'));
    }

    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();

        if (! $owner->payout_method || ! $owner->payout_details) {
            return back()->with('error', __('Set up a payout destination before requesting a withdrawal.'));
        }

        $validated = $request->validate([
            'currency' => ['required', 'string', 'size:3'],
        ]);

        try {
            $payout = DB::transaction(function () use ($owner, $validated) {
                // Row-lock the candidate payments for the duration of the check+insert, the same
                // way Booking::store() locks the pitch — otherwise two concurrent payout requests
                // could both see the same "unclaimed" payments and pay the owner out twice for them.
                $payments = $this->availableBalanceQuery($owner->id)
                    ->where('currency', $validated['currency'])
                    ->lockForUpdate()
                    ->get();

                if ($payments->isEmpty()) {
                    throw ValidationException::withMessages([
                        'currency' => __('Nothing available to withdraw in this currency.'),
                    ]);
                }

                $payout = Payout::create([
                    'owner_id' => $owner->id,
                    'amount' => $payments->sum('amount'),
                    'currency' => $validated['currency'],
                    'method' => $owner->payout_method,
                    'destination' => $owner->payout_details,
                    'status' => 'pending',
                    'reference' => 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                ]);

                foreach ($payments as $payment) {
                    PayoutItem::create([
                        'payout_id' => $payout->id,
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                    ]);
                }

                return $payout;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', __('Payout of :amount :currency requested.', [
            'amount' => number_format((float) $payout->amount, 2),
            'currency' => $payout->currency,
        ]));
    }

    public function markPaid(Request $request, Payout $payout): RedirectResponse
    {
        abort_unless(in_array($payout->status, ['pending', 'processing']), 422);

        $payout->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', __('Payout marked as paid.'));
    }

    public function markFailed(Request $request, Payout $payout): RedirectResponse
    {
        abort_unless(in_array($payout->status, ['pending', 'processing']), 422);

        // No need to touch payout_items — a failed payout is simply excluded from the "locked"
        // check below, so its payments become available for the owner to withdraw again.
        $payout->update(['status' => 'failed']);

        return back()->with('success', __('Payout marked as failed — its payments are available to withdraw again.'));
    }

    /**
     * Successful payments for the owner's pitches that aren't already claimed by an
     * active (non-failed) payout.
     */
    private function availableBalanceQuery(int $ownerId): Builder
    {
        return Payment::query()
            ->whereHas('booking.pitch', fn ($q) => $q->where('owner_id', $ownerId))
            ->where('status', 'successful')
            ->whereDoesntHave('payoutItem', function ($q) {
                $q->whereHas('payout', fn ($qq) => $qq->whereIn('status', ['pending', 'processing', 'paid']));
            });
    }
}
