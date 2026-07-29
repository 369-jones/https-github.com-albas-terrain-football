@extends('layouts.public')

@section('title', $pitch->nameFor())

@section('content')

    @php
        $currency = session('currency', config('currencies.default'));
        $currencyService = app(\App\Services\CurrencyService::class);
        $displayPrice = $currencyService->convert((float) $pitch->price_per_hour, $pitch->currency, $currency);
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">

        <a href="{{ route('home') }}" class="text-sm text-ink/60 hover:text-pitch-700">&larr; {{ __('Back to search') }}</a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">

            <div class="lg:col-span-2">
                <div class="aspect-[16/9] bg-pitch-100 rounded-2xl overflow-hidden">
                    @if ($pitch->images->first())
                        <img src="{{ $pitch->images->first()->url() }}" alt="{{ $pitch->nameFor() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-pitch-700/40 text-5xl"><i class="fa-solid fa-futbol"></i></div>
                    @endif
                </div>

                <h1 class="font-display font-bold text-3xl mt-6">{{ $pitch->nameFor() }}</h1>
                <p class="text-ink/60 mt-1">{{ $pitch->address ? $pitch->address.', ' : '' }}{{ $pitch->city }}</p>

                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="bg-pitch-50 text-pitch-800 text-xs font-medium px-3 py-1.5 rounded-full">{{ $pitch->capacity }}v{{ $pitch->capacity }}</span>
                    <span class="bg-pitch-50 text-pitch-800 text-xs font-medium px-3 py-1.5 rounded-full">{{ __(ucfirst(str_replace('_', ' ', $pitch->surface_type))) }}</span>
                    @foreach (($pitch->amenities ?? []) as $amenity)
                        <span class="bg-pitch-50 text-pitch-800 text-xs font-medium px-3 py-1.5 rounded-full">{{ __(ucfirst(str_replace('_', ' ', $amenity))) }}</span>
                    @endforeach
                </div>

                @if ($pitch->descriptionFor())
                    <p class="mt-6 text-ink/80 leading-relaxed">{{ $pitch->descriptionFor() }}</p>
                @endif

                {{-- Availability: scoreboard-style slot grid --}}
                <div class="mt-10" x-data="{ selected: null }">
                    <h2 class="font-display font-bold text-xl mb-4">{{ __('Availability') }}</h2>

                    <div class="flex gap-2 overflow-x-auto pb-2 mb-4">
                        @foreach ($dateOptions as $date)
                            <a href="{{ route('pitches.show', [$pitch->slug, 'date' => $date->format('Y-m-d')]) }}"
                               class="flex-shrink-0 flex flex-col items-center px-4 py-2 rounded-xl border text-sm
                                      {{ $date->isSameDay($selectedDate) ? 'bg-pitch-800 text-sand-100 border-pitch-800' : 'border-line hover:border-pitch-500' }}">
                                <span class="uppercase text-xs opacity-70">{{ $date->translatedFormat('D') }}</span>
                                <span class="font-score font-semibold">{{ $date->format('d/m') }}</span>
                            </a>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('bookings.store', $pitch) }}" class="bg-ink rounded-2xl p-5">
                        @csrf
                        <input type="hidden" name="booking_date" value="{{ $selectedDate->format('Y-m-d') }}">
                        <input type="hidden" name="start_time" x-model="selected?.start">
                        <input type="hidden" name="end_time" x-model="selected?.end">

                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach ($slots as $slot)
                                <button type="button"
                                        @if(!$slot['available']) disabled @endif
                                        @click="selected = { start: '{{ $slot['start'] }}', end: '{{ $slot['end'] }}' }"
                                        :class="selected && selected.start === '{{ $slot['start'] }}'
                                            ? 'bg-amber-signal text-ink border-amber-signal'
                                            : '{{ $slot['available'] ? 'border-pitch-600 text-sand-100 hover:border-amber-signal' : 'border-ink/40 text-sand-100/30 line-through cursor-not-allowed' }}'"
                                        class="font-score text-sm border rounded-lg py-2 transition-colors">
                                    {{ $slot['start'] }}
                                </button>
                            @endforeach
                        </div>

                        <button type="submit"
                                x-bind:disabled="!selected"
                                :class="selected ? 'opacity-100' : 'opacity-40 cursor-not-allowed'"
                                class="mt-5 w-full bg-pitch-500 hover:bg-pitch-600 text-ink font-semibold py-3 rounded-xl transition-all">
                            <span x-show="selected">{{ __('Book') }} <span x-text="selected?.start"></span>–<span x-text="selected?.end"></span></span>
                            <span x-show="!selected">{{ __('Select a time slot') }}</span>
                        </button>
                    </form>
                </div>

                {{-- Reviews --}}
                <div class="mt-10">
                    <div class="flex items-center gap-3 mb-4">
                        <h2 class="font-display font-bold text-xl">{{ __('Reviews') }}</h2>
                        @if ($reviewsAvg)
                            <span class="text-sm text-amber-signal-dark font-medium">★ {{ number_format($reviewsAvg, 1) }} · {{ trans_choice(':count review|:count reviews', $pitch->reviews->count(), ['count' => $pitch->reviews->count()]) }}</span>
                        @endif
                    </div>

                    @if ($eligibleBookingToReview)
                        <form method="POST" action="{{ route('reviews.store', $pitch) }}" class="bg-white border border-line rounded-2xl p-5 mb-6" x-data="{ rating: 5 }">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $eligibleBookingToReview->id }}">
                            <p class="text-sm font-medium mb-2">{{ __('Rate your visit on :date', ['date' => $eligibleBookingToReview->booking_date->format('d/m/Y')]) }}</p>
                            <div class="flex gap-1 mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}"
                                            :class="rating >= {{ $i }} ? 'text-amber-signal-dark' : 'text-ink/20'"
                                            class="text-2xl leading-none">★</button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" x-model="rating">
                            <textarea name="comment" rows="3" placeholder="{{ __('Optional comment') }}"
                                      class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500"></textarea>
                            <button type="submit" class="mt-3 bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold px-5 py-2 rounded-xl text-sm transition-colors">
                                {{ __('Submit review') }}
                            </button>
                        </form>
                    @endif

                    @forelse ($pitch->reviews as $review)
                        <div class="border-b border-line py-4">
                            <div class="flex items-center justify-between">
                                <p class="font-medium">{{ $review->user->name }}</p>
                                <span class="text-amber-signal-dark text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="text-sm text-ink/70 mt-1">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-ink/50">{{ __('No reviews yet.') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Sticky price/summary sidebar --}}
            <aside class="lg:col-span-1">
                <div class="ticket-edge bg-white border border-line rounded-2xl p-5 sticky top-24">
                    <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Price') }}</p>
                    <p class="font-score font-bold text-3xl text-pitch-800 mt-1">
                        {{ $currencyService->format($displayPrice, $currency) }}
                        <span class="text-sm font-body font-normal text-ink/50">/{{ __('hour') }}</span>
                    </p>
                    <div class="mt-4 pt-4 border-t border-dashed border-line text-sm text-ink/70 space-y-1">
                        <p>{{ __('Free cancellation up to 24h before kickoff.') }}</p>
                        <p>{{ __('Payment secured via card or Mobile Money.') }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>

@endsection
