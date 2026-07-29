<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Pitch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pitch_id' => Pitch::factory(),
            'user_id' => User::factory(),
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'total_price' => 25,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];
    }
}
