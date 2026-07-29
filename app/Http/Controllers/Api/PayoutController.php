<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PayoutResource;
use App\Models\Payout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

// Deliberately self-scoped everywhere except markPaid/markFailed — a payout is tied to
// one specific payout_method/payout_details destination, so unlike pitches and bookings
// this is never widened for 'admin': nobody should see or claim another owner's balance
// through their own token. Admin oversight of money movement goes through the 'finance'
// role instead (mark paid/failed), exactly mirroring the web PayoutController.
class PayoutController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $owner = $request->user();

        $available = Payout::availableBalanceQuery($owner->id)
            ->get()
            ->groupBy('currency')
            ->map(fn ($payments) => (float) $payments->sum('amount'));

        return PayoutResource::collection(
            Payout::where('owner_id', $owner->id)->withCount('items')->latest()->paginate(20)
        )->additional(['available_balance' => $available]);
    }

    public function updateDestination(Request $request): JsonResponse
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

        return response()->json(['message' => __('Payout destination updated.')]);
    }

    public function store(Request $request): JsonResponse
    {
        $owner = $request->user();

        if (! $owner->payout_method || ! $owner->payout_details) {
            throw ValidationException::withMessages([
                'currency' => __('Set up a payout destination before requesting a withdrawal.'),
            ]);
        }

        $validated = $request->validate([
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $payout = Payout::request($owner, $validated['currency']);

        return (new PayoutResource($payout))->response()->setStatusCode(201);
    }

    public function markPaid(Request $request, Payout $payout): PayoutResource
    {
        abort_unless($request->user()->hasRole('finance'), 403);
        abort_unless(in_array($payout->status, ['pending', 'processing']), 422);

        $payout->update(['status' => 'paid', 'paid_at' => now()]);

        return new PayoutResource($payout);
    }

    public function markFailed(Request $request, Payout $payout): PayoutResource
    {
        abort_unless($request->user()->hasRole('finance'), 403);
        abort_unless(in_array($payout->status, ['pending', 'processing']), 422);

        $payout->update(['status' => 'failed']);

        return new PayoutResource($payout);
    }
}
