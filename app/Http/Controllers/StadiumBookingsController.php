<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pitch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Lets the admin (or a stadium manager, for their own stadium) browse marketplace
// bookings across every sport, with a stadium filter — the owner dashboard only shows
// the next 20 upcoming bookings with no filtering, this is the fuller list view.
class StadiumBookingsController extends Controller
{
    public function index(Request $request): View
    {
        $ownerId = $request->user()->hasRole('admin') ? null : $request->user()->id;

        $pitches = Pitch::when($ownerId, fn ($q) => $q->where('owner_id', $ownerId))
            ->orderBy('city')
            ->get(['id', 'name', 'city', 'sport']);

        $bookings = $this->filteredQuery($request, $ownerId)
            ->with(['pitch', 'user'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'pitches'));
    }

    private function filteredQuery(Request $request, ?int $ownerId): Builder
    {
        return Booking::query()
            ->when($ownerId, fn ($q) => $q->whereHas('pitch', fn ($p) => $p->where('owner_id', $ownerId)))
            ->when($request->filled('pitch_id'), fn ($q) => $q->where('pitch_id', $request->integer('pitch_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('booking_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('booking_date', '<=', $request->input('to')));
    }
}
