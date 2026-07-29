@extends('layouts.public')

@section('content')

    @php
        $hasFilters = request()->anyFilled(['surface', 'capacity', 'amenities', 'min_price', 'max_price', 'date']);
        $selectedSurfaces = (array) request('surface', []);
        $selectedAmenities = (array) request('amenities', []);
    @endphp

    <section class="bg-pitch-900 text-sand-100 relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-20 relative z-10">
            <p class="font-score uppercase tracking-widest text-amber-signal text-sm mb-3">
                {{ now()->format('l, H:i') }} — {{ __('kickoff is closer than you think') }}
            </p>
            <h1 class="font-display font-bold text-4xl sm:text-5xl leading-[1.05] max-w-2xl">
                {{ __('Find a pitch. Lock the slot. Play tonight.') }}
            </h1>
            <p class="mt-4 text-sand-100/80 max-w-xl">
                {{ __('Search verified 5-a-side, 7-a-side and 11-a-side pitches near you, and pay by card or Mobile Money in seconds.') }}
            </p>

            <form method="GET" action="{{ route('home') }}" x-data="{ open: {{ $hasFilters ? 'true' : 'false' }} }" class="mt-8 max-w-2xl">
                <div class="bg-sand-100 rounded-2xl p-2 flex flex-col sm:flex-row gap-2">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="{{ __('City or neighborhood') }}"
                           class="flex-1 px-4 py-3 rounded-xl text-ink placeholder:text-ink/40 focus:outline-none">
                    <button type="button" @click="open = !open"
                            class="flex items-center justify-center gap-1.5 bg-white text-ink font-medium px-4 py-3 rounded-xl border border-line hover:border-pitch-500 transition-colors">
                        {{ __('Filters') }}
                        @if ($hasFilters)
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-signal-dark"></span>
                        @endif
                    </button>
                    <button type="submit"
                            class="bg-amber-signal hover:bg-amber-signal-dark text-ink font-semibold px-6 py-3 rounded-xl transition-colors">
                        {{ __('Search') }}
                    </button>
                </div>

                {{-- Filter panel --}}
                <div x-show="open" x-cloak class="bg-sand-100 text-ink rounded-2xl p-5 mt-2 space-y-4">

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/50 mb-1.5">{{ __('Date') }}</label>
                        <input type="date" name="date" value="{{ request('date') }}" min="{{ now()->toDateString() }}"
                               class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/50 mb-1.5">{{ __('Format') }}</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ([5 => '5v5', 7 => '7v7', 11 => '11v11'] as $value => $label)
                                <label class="flex items-center gap-1.5 text-sm bg-white border border-line rounded-full px-3 py-1.5 cursor-pointer has-[:checked]:border-pitch-600 has-[:checked]:bg-pitch-50">
                                    <input type="radio" name="capacity" value="{{ $value }}" class="sr-only" @checked(request('capacity') == $value)>
                                    {{ $label }}
                                </label>
                            @endforeach
                            <label class="flex items-center gap-1.5 text-sm bg-white border border-line rounded-full px-3 py-1.5 cursor-pointer has-[:checked]:border-pitch-600 has-[:checked]:bg-pitch-50">
                                <input type="radio" name="capacity" value="" class="sr-only" @checked(! request('capacity'))>
                                {{ __('Any') }}
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/50 mb-1.5">{{ __('Surface') }}</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (\App\Http\Controllers\PitchController::SURFACE_TYPES as $type)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="surface[]" value="{{ $type }}" @checked(in_array($type, $selectedSurfaces))>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/50 mb-1.5">{{ __('Amenities') }}</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach (\App\Http\Controllers\PitchController::AMENITIES as $amenity)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity }}" @checked(in_array($amenity, $selectedAmenities))>
                                    {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-ink/50 mb-1.5">{{ __('Price per hour') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('Min') }}" min="0"
                                   class="w-28 border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                            <span class="text-ink/40">–</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('Max') }}" min="0"
                                   class="w-28 border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                            <span class="text-xs text-ink/40">{{ $currency }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-dashed border-line">
                        <a href="{{ route('home') }}" class="text-sm text-ink/60 hover:text-pitch-700">{{ __('Clear filters') }}</a>
                        <button type="submit" class="bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold px-5 py-2 rounded-xl text-sm transition-colors">
                            {{ __('Apply filters') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Subtle pitch-line pattern, not decoration for its own sake — reads as a field from above --}}
        <svg class="absolute -right-24 -bottom-24 w-[520px] h-[520px] opacity-[0.08] pointer-events-none" viewBox="0 0 400 400" fill="none">
            <rect x="20" y="20" width="360" height="360" rx="8" stroke="white" stroke-width="3"/>
            <circle cx="200" cy="200" r="60" stroke="white" stroke-width="3"/>
            <line x1="200" y1="20" x2="200" y2="380" stroke="white" stroke-width="3"/>
        </svg>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <div class="flex items-baseline justify-between mb-6 gap-4 flex-wrap">
            <h2 class="font-display font-bold text-2xl">
                {{ request('q') ? __('Results for :query', ['query' => request('q')]) : __('Popular pitches') }}
            </h2>

            <div class="flex items-center gap-3">
                <span class="text-sm text-ink/50">{{ trans_choice(':count pitch|:count pitches', $pitches->total(), ['count' => $pitches->total()]) }}</span>

                <form method="GET" action="{{ route('home') }}">
                    @foreach (request()->except(['sort', 'page']) as $key => $value)
                        @foreach (Illuminate\Support\Arr::wrap($value) as $v)
                            <input type="hidden" name="{{ is_array($value) ? "{$key}[]" : $key }}" value="{{ $v }}">
                        @endforeach
                    @endforeach
                    <select name="sort" onchange="this.form.submit()"
                            class="text-sm border border-line rounded-xl px-3 py-1.5 focus:outline-none focus:border-pitch-500 bg-white">
                        <option value="" @selected(! request('sort'))>{{ __('Newest') }}</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>{{ __('Price: low to high') }}</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>{{ __('Price: high to low') }}</option>
                        <option value="rating" @selected(request('sort') === 'rating')>{{ __('Top rated') }}</option>
                    </select>
                </form>
            </div>
        </div>

        @if ($pitches->isEmpty())
            <div class="text-center py-16 border border-dashed border-line rounded-2xl">
                <p class="font-display text-xl mb-1">{{ __('No pitches found here yet.') }}</p>
                <p class="text-ink/60 text-sm">{{ __('Try different filters, or check back soon.') }}</p>
                @if ($hasFilters || request('q'))
                    <a href="{{ route('home') }}" class="inline-block mt-3 text-pitch-700 font-medium hover:underline">{{ __('Clear filters') }}</a>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($pitches as $pitch)
                    <x-pitch-card :pitch="$pitch" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $pitches->links() }}
            </div>
        @endif
    </section>

@endsection
