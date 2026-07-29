<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnedPitches;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Pitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    use ScopesToOwnedPitches;

    public function index(Request $request): AnonymousResourceCollection
    {
        $pitchIds = $this->visiblePitchIds($request->user());

        $bookings = Booking::with(['pitch', 'user'])
            ->when($pitchIds !== null, fn ($q) => $q->whereIn('pitch_id', $pitchIds))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('booking_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('booking_date', '<=', $request->date('to')))
            ->latest('booking_date')
            ->paginate(20);

        return BookingResource::collection($bookings);
    }

    public function show(Request $request, Booking $booking): BookingResource
    {
        $pitchIds = $this->visiblePitchIds($request->user());

        abort_if($pitchIds !== null && ! $pitchIds->contains($booking->pitch_id), 403);

        return new BookingResource($booking->load(['pitch', 'user']));
    }

    /**
     * Manager/admin-entered booking (e.g. recording a phone or walk-in reservation).
     * Deliberately doesn't restrict booking_date to today-or-later like the public
     * booking flow does — this endpoint is also used to backfill past reservations.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pitch_id' => ['required', 'integer', 'exists:pitches,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['nullable', 'in:pending,confirmed,cancelled,completed,no_show'],
            'payment_status' => ['nullable', 'in:unpaid,paid,refunded,failed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $pitch = Pitch::findOrFail($validated['pitch_id']);
        $this->authorizePitchManagement($pitch, $request->user());

        $booking = DB::transaction(function () use ($pitch, $validated) {
            // Same lock-then-check pattern as the public booking flow (BookingController::store)
            // to prevent two concurrent writes from double-booking the same slot.
            Pitch::whereKey($pitch->id)->lockForUpdate()->first();

            if (Booking::overlaps($pitch->id, $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                throw ValidationException::withMessages([
                    'start_time' => __('This time slot is already booked. Please choose another.'),
                ]);
            }

            $hours = (strtotime($validated['end_time']) - strtotime($validated['start_time'])) / 3600;

            return Booking::create([
                'pitch_id' => $pitch->id,
                'user_id' => $validated['user_id'],
                'booking_date' => $validated['booking_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'total_price' => $pitch->price_per_hour * $hours,
                'currency' => $pitch->currency,
                'status' => $validated['status'] ?? 'confirmed',
                'payment_status' => $validated['payment_status'] ?? 'unpaid',
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return (new BookingResource($booking->load(['pitch', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Booking $booking): BookingResource
    {
        $this->authorizePitchManagement($booking->pitch, $request->user());

        $validated = $request->validate([
            'status' => ['sometimes', 'in:pending,confirmed,cancelled,completed,no_show'],
            'payment_status' => ['sometimes', 'in:unpaid,paid,refunded,failed'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        abort_if(empty($validated), 422, 'Nothing to update — provide status, payment_status, and/or notes.');

        $booking->update($validated);

        return new BookingResource($booking->fresh(['pitch', 'user']));
    }
}
