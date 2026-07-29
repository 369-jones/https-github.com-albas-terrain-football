<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
