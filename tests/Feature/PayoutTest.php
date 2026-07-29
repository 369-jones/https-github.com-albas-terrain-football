<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Pitch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        Role::firstOrCreate(['name' => 'owner']);
        Role::firstOrCreate(['name' => 'finance']);

        $owner = User::factory()->create([
            'payout_method' => 'bank_transfer',
            'payout_details' => ['bank_name' => 'Ecobank', 'account_name' => 'Owner', 'account_number' => '123'],
        ]);
        $owner->assignRole('owner');

        return $owner;
    }

    private function successfulPayment(Pitch $pitch, float $amount = 15000, ?string $startTime = null): Payment
    {
        $booking = Booking::factory()->create([
            'pitch_id' => $pitch->id,
            'total_price' => $amount,
            'currency' => $pitch->currency,
            'start_time' => $startTime ?? '10:00',
            'end_time' => $startTime ? date('H:i', strtotime($startTime) + 3600) : '11:00',
        ]);

        return Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'provider' => 'paystack',
            'reference' => 'PAY-'.$booking->id.'-'.uniqid(),
            'amount' => $amount,
            'currency' => $pitch->currency,
            'status' => 'successful',
            'paid_at' => now(),
        ]);
    }

    public function test_requesting_a_payout_claims_all_available_payments_and_hides_them_from_the_balance(): void
    {
        $owner = $this->owner();
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000, '10:00');
        $this->successfulPayment($pitch, 25000, '14:00');

        $response = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);

        $response->assertSessionHasNoErrors();
        $this->assertSame(1, Payout::count());
        $this->assertSame('40000.00', Payout::first()->amount);
        $this->assertSame(2, Payout::first()->items()->count());
    }

    public function test_requesting_a_payout_twice_does_not_double_count_the_same_payments(): void
    {
        $owner = $this->owner();
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000);

        $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $response = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);

        $response->assertSessionHasErrors('currency');
        $this->assertSame(1, Payout::count());
    }

    public function test_a_payment_from_another_owner_is_never_included(): void
    {
        $owner = $this->owner();
        $otherOwner = User::factory()->create();
        $otherPitch = Pitch::factory()->create(['owner_id' => $otherOwner->id, 'currency' => 'XOF']);
        $this->successfulPayment($otherPitch, 99000);

        $response = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);

        $response->assertSessionHasErrors('currency');
        $this->assertSame(0, Payout::count());
    }

    public function test_marking_a_payout_failed_frees_its_payments_for_a_future_payout(): void
    {
        $owner = $this->owner();
        $owner->assignRole('finance');
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000);

        $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $payout = Payout::first();

        $this->actingAs($owner)->post(route('admin.payouts.mark-failed', $payout));
        $this->assertSame('failed', $payout->fresh()->status);

        $second = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $second->assertSessionHasNoErrors();
        $this->assertSame(2, Payout::count());
    }

    public function test_a_paid_payout_does_not_free_its_payments(): void
    {
        $owner = $this->owner();
        $owner->assignRole('finance');
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000);

        $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $payout = Payout::first();

        $this->actingAs($owner)->post(route('admin.payouts.mark-paid', $payout));
        $this->assertSame('paid', $payout->fresh()->status);

        $second = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $second->assertSessionHasErrors('currency');
        $this->assertSame(1, Payout::count());
    }

    public function test_only_finance_role_can_mark_a_payout_paid_or_failed(): void
    {
        $owner = $this->owner(); // has 'owner' role only, not 'finance'
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000);
        $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);
        $payout = Payout::first();

        $this->actingAs($owner)->post(route('admin.payouts.mark-paid', $payout))->assertForbidden();
        $this->actingAs($owner)->post(route('admin.payouts.mark-failed', $payout))->assertForbidden();
        $this->assertSame('pending', $payout->fresh()->status);
    }

    public function test_cannot_request_a_payout_without_a_destination_configured(): void
    {
        Role::firstOrCreate(['name' => 'owner']);
        $owner = User::factory()->create(); // no payout_method set
        $owner->assignRole('owner');
        $pitch = Pitch::factory()->create(['owner_id' => $owner->id, 'currency' => 'XOF']);
        $this->successfulPayment($pitch, 15000);

        $response = $this->actingAs($owner)->post(route('admin.payouts.store'), ['currency' => 'XOF']);

        $response->assertSessionHas('error');
        $this->assertSame(0, Payout::count());
    }
}
