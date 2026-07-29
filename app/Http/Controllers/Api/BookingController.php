<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnedPitches;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
