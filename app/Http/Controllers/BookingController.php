<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pitch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function mine(Request $request): View
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->with('pitch')
            ->orderByDesc('booking_date')
            ->paginate(10);

        return view('bookings.mine', compact('bookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        $booking->load('pitch', 'payment');

        return view('bookings.show', compact('booking'));
    }

    public function store(Request $request, Pitch $pitch): RedirectResponse
    {
        $validated = $request->validate([
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $booking = DB::transaction(function () use ($pitch, $validated, $request) {
            // Lock the pitch row for the duration of the check+insert to prevent a race
            // where two users book the same slot at the same instant.
            Pitch::whereKey($pitch->id)->lockForUpdate()->first();

            if (Booking::overlaps($pitch->id, $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                throw ValidationException::withMessages([
                    'start_time' => __('This time slot is already booked. Please choose another.'),
                ]);
            }

            $hours = (strtotime($validated['end_time']) - strtotime($validated['start_time'])) / 3600;

            return Booking::create([
                'pitch_id' => $pitch->id,
                'user_id' => $request->user()->id,
                'booking_date' => $validated['booking_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'total_price' => $pitch->price_per_hour * $hours,
                'currency' => $pitch->currency,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);
        });

        return redirect()
            ->route('payments.checkout', $booking)
            ->with('success', __('Booking created — please complete payment to confirm it.'));
    }
}
