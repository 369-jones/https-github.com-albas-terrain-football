@extends('layouts.public')

@section('title', __('My bookings'))

@section('content')

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <h1 class="font-display font-bold text-3xl mb-8">{{ __('My bookings') }}</h1>

        @forelse ($bookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}"
               class="flex items-center justify-between bg-white border border-line rounded-xl px-5 py-4 mb-3 hover:border-pitch-500 transition-colors">
                <div>
                    <p class="font-medium">{{ $booking->pitch->nameFor() }}</p>
                    <p class="text-sm text-ink/60">{{ $booking->pitch->city }} · {{ $booking->booking_date->format('d/m/Y') }} · {{ $booking->start_time }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    {{ match($booking->status) {
                        'confirmed' => 'bg-pitch-100 text-pitch-800',
                        'pending' => 'bg-amber-signal/20 text-amber-signal-dark',
                        'cancelled' => 'bg-red-50 text-red-600',
                        default => 'bg-ink/10 text-ink/50',
                    } }}">
                    {{ __(ucfirst($booking->status)) }}
                </span>
            </a>
        @empty
            <div class="text-center py-16 border border-dashed border-line rounded-2xl">
                <p class="font-display text-xl mb-1">{{ __('No bookings yet.') }}</p>
                <a href="{{ route('home') }}" class="text-pitch-700 font-medium hover:underline">{{ __('Find a pitch') }} &rarr;</a>
            </div>
        @endforelse

        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>

@endsection
