@extends('layouts.public')

@section('title', __('Earnings'))

@section('content')

    @php $currencyService = app(\App\Services\CurrencyService::class); @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

        @include('admin._nav', ['active' => 'admin.earnings'])

        <div class="flex items-center justify-between mb-8 gap-4 flex-wrap">
            <h1 class="font-display font-bold text-3xl">{{ __('Earnings') }}</h1>
            <a href="{{ route('admin.earnings.export', request()->query()) }}"
               class="bg-white border border-line hover:border-pitch-500 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                {{ __('Export CSV') }}
            </a>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-10">
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Total earned') }}</p>
                <p class="font-score font-bold text-2xl text-pitch-800 mt-1">{{ $currencyService->format($summary['total_earned'], $currency) }}</p>
            </div>
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('This month') }}</p>
                <p class="font-score font-bold text-2xl text-pitch-800 mt-1">{{ $currencyService->format($summary['this_month'], $currency) }}</p>
            </div>
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Pending') }}</p>
                <p class="font-score font-bold text-2xl text-amber-signal-dark mt-1">{{ $currencyService->format($summary['pending'], $currency) }}</p>
            </div>
            <div class="bg-white border border-line rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wide text-ink/50">{{ __('Refunded') }}</p>
                <p class="font-score font-bold text-2xl text-red-600 mt-1">{{ $currencyService->format($summary['refunded'], $currency) }}</p>
            </div>
        </div>

        {{-- Per-pitch breakdown --}}
        @if ($byPitch->isNotEmpty())
            <div class="mb-10">
                <h2 class="font-display font-bold text-xl mb-4">{{ __('By pitch') }}</h2>
                <div class="bg-white border border-line rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-pitch-50 text-ink/60 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium">{{ __('Pitch') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Paid bookings') }}</th>
                                <th class="px-4 py-3 font-medium">{{ __('Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($byPitch as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $row['pitch']->nameFor() }}</td>
                                    <td class="px-4 py-3 font-score">{{ $row['count'] }}</td>
                                    <td class="px-4 py-3 font-score font-semibold text-pitch-800">{{ $currencyService->format($row['total'], $currency) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.earnings') }}" class="bg-white border border-line rounded-2xl p-4 mb-6 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Pitch') }}</label>
                <select name="pitch_id" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    <option value="">{{ __('All pitches') }}</option>
                    @foreach ($pitches as $pitch)
                        <option value="{{ $pitch->id }}" @selected(request('pitch_id') == $pitch->id)>{{ $pitch->nameFor() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Status') }}</label>
                <select name="status" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach (['pending', 'successful', 'failed', 'refunded'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Provider') }}</label>
                <select name="provider" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    <option value="">{{ __('All providers') }}</option>
                    @foreach (['paystack', 'flutterwave'] as $provider)
                        <option value="{{ $provider }}" @selected(request('provider') === $provider)>{{ ucfirst($provider) }}</option>
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
            @if (request()->anyFilled(['pitch_id', 'status', 'provider', 'from', 'to']))
                <a href="{{ route('admin.earnings') }}" class="text-sm text-ink/60 hover:text-pitch-700">{{ __('Clear') }}</a>
            @endif
        </form>

        {{-- Payments table --}}
        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-pitch-50 text-ink/60 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Date') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Pitch') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Player') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Provider') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Amount') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-3 font-score text-ink/70">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $payment->booking->pitch->nameFor() }}</td>
                            <td class="px-4 py-3">{{ $payment->booking->user->name }}</td>
                            <td class="px-4 py-3 text-ink/70">{{ ucfirst($payment->provider) }}</td>
                            <td class="px-4 py-3 font-score font-semibold">{{ $currencyService->format((float) $payment->amount, $payment->currency) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                    {{ match($payment->status) {
                                        'successful' => 'bg-pitch-100 text-pitch-800',
                                        'pending' => 'bg-amber-signal/20 text-amber-signal-dark',
                                        'refunded' => 'bg-red-50 text-red-600',
                                        default => 'bg-ink/10 text-ink/50',
                                    } }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-ink/50">{{ __('No payments match these filters.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $payments->links() }}</div>
    </div>

@endsection
