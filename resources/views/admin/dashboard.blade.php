@extends('layouts.public')

@section('title', __('Owner dashboard'))

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

        @include('admin._nav', ['active' => 'admin.dashboard'])

        <div class="flex items-center justify-between mb-8">
            <h1 class="font-display font-bold text-3xl">{{ __('Your pitches') }}</h1>
            @if (Route::has('admin.pitches.create'))
                <a href="{{ route('admin.pitches.create') }}"
                   class="bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    {{ __('Add a pitch') }}
                </a>
            @else
                <span class="bg-ink/10 text-ink/40 font-semibold px-5 py-2.5 rounded-xl cursor-not-allowed" title="{{ __('Coming soon') }}">
                    {{ __('Add a pitch') }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Bookings this week') }}</p>
                <p class="font-score font-bold text-3xl text-pitch-800 mt-1">{{ $stats['week_bookings'] }}</p>
            </div>
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Revenue this month') }}</p>
                <p class="font-score font-bold text-3xl text-pitch-800 mt-1">
                    {{ app(\App\Services\CurrencyService::class)->format($stats['month_revenue'], $stats['currency']) }}
                </p>
            </div>
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Occupancy rate') }}</p>
                <p class="font-score font-bold text-3xl text-pitch-800 mt-1">{{ $stats['occupancy'] }}%</p>
            </div>
        </div>

        <div class="bg-white border border-line rounded-2xl overflow-hidden mb-10">
            <table class="w-full text-sm">
                <thead class="bg-pitch-50 text-ink/60 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Pitch') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('City') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Price/hour') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($pitches as $pitch)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $pitch->nameFor() }}</td>
                            <td class="px-4 py-3 text-ink/70">{{ $pitch->city }}</td>
                            <td class="px-4 py-3 font-score">{{ app(\App\Services\CurrencyService::class)->format((float) $pitch->price_per_hour, $pitch->currency) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $pitch->is_active ? 'bg-pitch-100 text-pitch-800' : 'bg-ink/10 text-ink/50' }}">
                                    {{ $pitch->is_active ? __('Active') : __('Hidden') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (Route::has('admin.pitches.edit'))
                                    <a href="{{ route('admin.pitches.edit', $pitch) }}" class="text-pitch-700 hover:underline">{{ __('Edit') }}</a>
                                @else
                                    <span class="text-ink/30">{{ __('Edit') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-ink/50">{{ __('No pitches yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="font-display font-bold text-xl mb-4">{{ __('Upcoming bookings') }}</h2>
        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-pitch-50 text-ink/60 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Player') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Pitch') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('When') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($upcomingBookings as $booking)
                        <tr>
                            <td class="px-4 py-3">{{ $booking->user->name }}</td>
                            <td class="px-4 py-3">{{ $booking->pitch->nameFor() }}</td>
                            <td class="px-4 py-3 font-score">{{ $booking->booking_date->format('d/m') }} · {{ $booking->start_time }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-pitch-100 text-pitch-800">
                                    {{ __(ucfirst($booking->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-ink/50">{{ __('No upcoming bookings yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
