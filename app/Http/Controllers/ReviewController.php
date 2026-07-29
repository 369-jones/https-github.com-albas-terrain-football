<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, \App\Models\Pitch $pitch): RedirectResponse
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = Booking::where('id', $validated['booking_id'])
            ->where('pitch_id', $pitch->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->firstOrFail();

        Review::create([
            'pitch_id' => $pitch->id,
            'user_id' => $request->user()->id,
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', __('Thanks for your review!'));
    }
}
