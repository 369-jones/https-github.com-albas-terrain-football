@extends('layouts.public')

@section('title', __('Bookings'))

@section('content')

    @php $currencyService = app(\App\Services\CurrencyService::class); @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

        @include('admin._nav', ['active' => 'admin.bookings'])

        <h1 class="font-display font-bold text-3xl mb-8">{{ __('Bookings') }}</h1>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.bookings') }}" class="bg-white border border-line rounded-2xl p-4 mb-6 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Stadium') }}</label>
                <select name="pitch_id" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    <option value="">{{ __('All stadiums') }}</option>
                    @foreach ($pitches as $pitch)
                        <option value="{{ $pitch->id }}" @selected(request('pitch_id') == $pitch->id)>{{ $pitch->nameFor() }} ({{ __(ucfirst($pitch->sport)) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Status') }}</label>
                <select name="status" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach (['pending', 'confirmed', 'cancelled', 'completed', 'no_show'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst(str_replace('_', ' ', $status))) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
            </div>
            <button type="submit" class="bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold px-5 py-2 rounded-xl text-sm transition-colors">
                {{ __('Filter') }}
            </button>
            @if (request()->anyFilled(['pitch_id', 'status', 'from', 'to']))
                <a href="{{ route('admin.bookings') }}" class="text-sm text-ink/60 hover:text-pitch-700">{{ __('Clear') }}</a>
            @endif
        </form>

        {{-- Bookings table --}}
        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-pitch-50 text-ink/60 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('When') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Stadium') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Player') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Payment') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($bookings as $booking)
                        <tr>
                            <td class="px-4 py-3 font-score text-ink/70">{{ $booking->booking_date->format('d/m/Y') }} · {{ $booking->start_time }}</td>
                            <td class="px-4 py-3">
                                <i class="fa-solid {{ $booking->pitch->sportIcon() }} text-ink/40 mr-1.5"></i>{{ $booking->pitch->nameFor() }}
                            </td>
                            <td class="px-4 py-3">{{ $booking->user->name }}</td>
                            <td class="px-4 py-3 font-score font-semibold">{{ $currencyService->format((float) $booking->total_price, $booking->currency) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                    {{ match($booking->status) {
                                        'confirmed' => 'bg-pitch-100 text-pitch-800',
                                        'pending' => 'bg-amber-signal/20 text-amber-signal-dark',
                                        'cancelled' => 'bg-red-50 text-red-600',
                                        'completed' => 'bg-ink/10 text-ink/60',
                                        default => 'bg-ink/10 text-ink/50',
                                    } }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $booking->status))) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-ink/70">{{ __(ucfirst($booking->payment_status)) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-ink/50">{{ __('No bookings match these filters.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>

@endsection
