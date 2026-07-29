@props(['pitch'])

@php
    $currency = session('currency', config('currencies.default'));
    $displayPrice = app(\App\Services\CurrencyService::class)->convert(
        (float) $pitch->price_per_hour, $pitch->currency, $currency
    );
@endphp

<a href="{{ route('pitches.show', $pitch->slug) }}"
   class="group block bg-white rounded-2xl overflow-hidden border border-line hover:border-pitch-500 hover:shadow-lg transition-all">
    <div class="aspect-[4/3] bg-pitch-100 relative overflow-hidden">
        @if ($pitch->images->first())
            <img src="{{ $pitch->images->first()->url() }}" alt="{{ $pitch->nameFor() }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-pitch-700/40 text-3xl"><i class="fa-solid {{ $pitch->sportIcon() }}"></i></div>
        @endif
        <span class="absolute top-3 left-3 bg-ink/80 text-sand-100 text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1.5">
            <i class="fa-solid {{ $pitch->sportIcon() }}"></i> {{ $pitch->capacity }}v{{ $pitch->capacity }}
        </span>
    </div>

    <div class="p-4">
        <h3 class="font-display font-semibold text-lg leading-snug">{{ $pitch->nameFor() }}</h3>
        <p class="text-sm text-ink/60 mt-0.5">{{ $pitch->city }}</p>

        <div class="mt-3 flex items-center justify-between">
            <span class="font-score text-pitch-800 font-semibold">
                {{ app(\App\Services\CurrencyService::class)->format($displayPrice, $currency) }}
                <span class="text-ink/50 font-body font-normal text-xs">/{{ __('hr') }}</span>
            </span>
            @if ($pitch->reviews_avg_rating)
                <span class="text-sm text-amber-signal-dark font-medium">★ {{ number_format($pitch->reviews_avg_rating, 1) }}</span>
            @endif
        </div>
    </div>
</a>
