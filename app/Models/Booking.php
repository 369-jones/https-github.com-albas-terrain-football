<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'pitch_id', 'user_id', 'booking_date', 'start_time', 'end_time',
        'total_price', 'currency', 'status', 'payment_status', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function pitch(): BelongsTo
    {
        return $this->belongsTo(Pitch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Check whether a given pitch/date/time-range is already booked.
     * Use this inside a DB transaction with a lock before creating a booking.
     */
    public static function overlaps(int $pitchId, string $date, string $start, string $end): bool
    {
        return static::query()
            ->where('pitch_id', $pitchId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->exists();
    }
}
