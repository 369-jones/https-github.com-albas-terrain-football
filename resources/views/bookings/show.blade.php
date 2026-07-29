@extends('layouts.public')

@section('title', __('Booking confirmed'))

@section('content')

    @php
        $currencyService = app(\App\Services\CurrencyService::class);
        $isPaid = $booking->payment_status === 'paid';
    @endphp

    <div class="max-w-md mx-auto px-4 sm:px-6 py-12 text-center">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 {{ $isPaid ? 'bg-pitch-100 text-pitch-700' : 'bg-amber-signal/20 text-amber-signal-dark' }}">
            <span class="text-2xl">{{ $isPaid ? '✓' : '⏳' }}</span>
        </div>

        <h1 class="font-display font-bold text-2xl">
            {{ $isPaid ? __('You\'re all set!') : __('Payment pending') }}
        </h1>
        <p class="text-ink/60 mt-1">
            {{ $isPaid ? __('Show this confirmation at the pitch.') : __('We\'ll confirm as soon as payment clears.') }}
        </p>

        <div class="ticket-edge bg-pitch-900 text-sand-100 rounded-2xl p-6 mt-8 text-left">
            <p class="text-xs uppercase tracking-widest text-amber-signal">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
            <h2 class="font-display font-bold text-xl mt-1">{{ $booking->pitch->nameFor() }}</h2>
            <p class="text-sand-100/70 text-sm">{{ $booking->pitch->city }}</p>

            <div class="grid grid-cols-2 gap-4 mt-6 font-score">
                <div>
                    <p class="text-xs text-sand-100/50 uppercase">{{ __('Date') }}</p>
                    <p class="font-semibold">{{ $booking->booking_date->translatedFormat('D d M') }}</p>
                </div>
                <div>
                    <p class="text-xs text-sand-100/50 uppercase">{{ __('Kickoff') }}</p>
                    <p class="font-semibold">{{ $booking->start_time }}–{{ $booking->end_time }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('bookings.mine') }}" class="inline-block mt-8 text-pitch-700 font-medium hover:underline">
            {{ __('View all my bookings') }} &rarr;
        </a>
    </div>

@endsection
