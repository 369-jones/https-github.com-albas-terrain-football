@extends('layouts.public')

@section('title', __('Checkout'))

@section('content')

    @php
        $currencyService = app(\App\Services\CurrencyService::class);
    @endphp

    <div class="max-w-md mx-auto px-4 sm:px-6 py-12">

        <h1 class="font-display font-bold text-2xl mb-6 text-center">{{ __('Confirm & pay') }}</h1>

        {{-- Signature element: match-day ticket stub --}}
        <div class="ticket-edge bg-pitch-900 text-sand-100 rounded-2xl p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs uppercase tracking-widest text-amber-signal">{{ __('Booking') }}</p>
                    <h2 class="font-display font-bold text-xl mt-1">{{ $booking->pitch->nameFor() }}</h2>
                    <p class="text-sand-100/70 text-sm">{{ $booking->pitch->city }}</p>
                </div>
                <span class="font-score text-xs bg-white/10 px-2 py-1 rounded">#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>

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

            <div class="mt-6 pt-6 border-t border-dashed border-sand-100/20 flex justify-between items-baseline">
                <span class="text-sm text-sand-100/70">{{ __('Total due') }}</span>
                <span class="font-score font-bold text-2xl">{{ $currencyService->format((float) $booking->total_price, $booking->currency) }}</span>
            </div>
        </div>

        <p class="text-sm font-medium text-ink/70 mt-8 mb-3">{{ __('Payment method') }}</p>

        <div class="space-y-3">
            <form method="POST" action="{{ route('payments.pay', [$booking, 'flutterwave']) }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between border border-line rounded-xl px-4 py-4 hover:border-pitch-500 transition-colors bg-white">
                    <span class="font-medium">{{ __('Mobile Money') }} <span class="text-ink/50 text-sm">— Orange, MTN, Wave...</span></span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <form method="POST" action="{{ route('payments.pay', [$booking, 'paystack']) }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between border border-line rounded-xl px-4 py-4 hover:border-pitch-500 transition-colors bg-white">
                    <span class="font-medium">{{ __('Card') }} <span class="text-ink/50 text-sm">— Visa, Mastercard</span></span>
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>
        </div>

        <p class="text-xs text-ink/50 text-center mt-6">{{ __('You will be redirected to a secure payment page. We never store your card or Mobile Money PIN.') }}</p>
    </div>

@endsection
