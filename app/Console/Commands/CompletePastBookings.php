<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class CompletePastBookings extends Command
{
    protected $signature = 'bookings:complete-past';

    protected $description = 'Mark confirmed bookings whose date has passed as completed, so players can leave reviews.';

    public function handle(): int
    {
        $count = Booking::where('status', 'confirmed')
            ->where('booking_date', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        $this->info("Marked {$count} booking(s) as completed.");

        return self::SUCCESS;
    }
}
