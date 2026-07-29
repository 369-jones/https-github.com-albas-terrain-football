<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Pitch;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingOverlapTest extends TestCase
{
    use RefreshDatabase;

    public function test_overlaps_detects_intersecting_ranges(): void
    {
        $pitch = Pitch::factory()->create();
        Booking::factory()->create([
            'pitch_id' => $pitch->id,
            'booking_date' => '2026-08-10',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'confirmed',
        ]);

        $this->assertTrue(Booking::overlaps($pitch->id, '2026-08-10', '10:30', '11:30'));
        $this->assertTrue(Booking::overlaps($pitch->id, '2026-08-10', '09:30', '10:30'));
        $this->assertTrue(Booking::overlaps($pitch->id, '2026-08-10', '10:00', '11:00'));
    }

    public function test_overlaps_ignores_non_intersecting_ranges(): void
    {
        $pitch = Pitch::factory()->create();
        Booking::factory()->create([
            'pitch_id' => $pitch->id,
            'booking_date' => '2026-08-10',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'confirmed',
        ]);

        $this->assertFalse(Booking::overlaps($pitch->id, '2026-08-10', '11:00', '12:00'));
        $this->assertFalse(Booking::overlaps($pitch->id, '2026-08-10', '09:00', '10:00'));
        $this->assertFalse(Booking::overlaps($pitch->id, '2026-08-11', '10:00', '11:00'));
    }

    public function test_overlaps_ignores_cancelled_bookings(): void
    {
        $pitch = Pitch::factory()->create();
        Booking::factory()->create([
            'pitch_id' => $pitch->id,
            'booking_date' => '2026-08-10',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'cancelled',
        ]);

        $this->assertFalse(Booking::overlaps($pitch->id, '2026-08-10', '10:00', '11:00'));
    }

    public function test_http_booking_request_rejects_an_already_booked_slot(): void
    {
        $user = User::factory()->create();
        $pitch = Pitch::factory()->create();
        Booking::factory()->create([
            'pitch_id' => $pitch->id,
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.store', $pitch), [
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertSame(1, Booking::where('pitch_id', $pitch->id)->count());
    }

    public function test_database_unique_constraint_blocks_duplicate_slot_even_if_app_check_is_bypassed(): void
    {
        $pitch = Pitch::factory()->create();
        $user = User::factory()->create();

        $attributes = [
            'pitch_id' => $pitch->id,
            'user_id' => $user->id,
            'booking_date' => '2026-08-10',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'total_price' => 15000,
            'currency' => 'XOF',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];

        Booking::create($attributes);

        $this->expectException(QueryException::class);
        Booking::create($attributes);
    }
}
