<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Payout extends Model
{
    protected $fillable = [
        'owner_id', 'amount', 'currency', 'method', 'destination',
        'status', 'reference', 'notes', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'destination' => 'array',
        'paid_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayoutItem::class);
    }

    /**
     * Successful payments for the owner's pitches that aren't already claimed by an
     * active (non-failed) payout.
     */
    public static function availableBalanceQuery(int $ownerId): Builder
    {
        return Payment::query()
            ->whereHas('booking.pitch', fn ($q) => $q->where('owner_id', $ownerId))
            ->where('status', 'successful')
            ->whereDoesntHave('payoutItem', function ($q) {
                $q->whereHas('payout', fn ($qq) => $qq->whereIn('status', ['pending', 'processing', 'paid']));
            });
    }

    /**
     * Claim the owner's whole available balance in one currency into a new pending payout.
     * Shared by the web and API controllers so the locking/creation logic — the part that
     * actually prevents double-paying an owner — only exists in one place.
     *
     * @throws ValidationException if nothing is available to withdraw in that currency.
     */
    public static function request(User $owner, string $currency): self
    {
        return DB::transaction(function () use ($owner, $currency) {
            // Row-lock the candidate payments for the duration of the check+insert, the same
            // way Booking::store() locks the pitch — otherwise two concurrent payout requests
            // could both see the same "unclaimed" payments and pay the owner out twice for them.
            $payments = static::availableBalanceQuery($owner->id)
                ->where('currency', $currency)
                ->lockForUpdate()
                ->get();

            if ($payments->isEmpty()) {
                throw ValidationException::withMessages([
                    'currency' => __('Nothing available to withdraw in this currency.'),
                ]);
            }

            $payout = static::create([
                'owner_id' => $owner->id,
                'amount' => $payments->sum('amount'),
                'currency' => $currency,
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
    }
}
