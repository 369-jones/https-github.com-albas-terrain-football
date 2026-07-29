@extends('layouts.public')

@section('title', __('Payouts'))

@section('content')

    @php $currencyService = app(\App\Services\CurrencyService::class); @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

        @include('admin._nav', ['active' => 'admin.payouts'])

        <h1 class="font-display font-bold text-3xl mb-8">{{ __('Payouts') }}</h1>

        {{-- Available to withdraw --}}
        <div class="mb-10">
            <h2 class="font-display font-bold text-xl mb-4">{{ __('Available to withdraw') }}</h2>

            @if ($available->isEmpty())
                <p class="text-sm text-ink/50">{{ __('Nothing to withdraw yet — earnings show up here once a booking is paid.') }}</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($available as $currencyCode => $amount)
                        <div class="ticket-edge bg-pitch-900 text-sand-100 rounded-2xl p-5">
                            <p class="text-xs uppercase tracking-wide text-sand-100/60">{{ $currencyCode }}</p>
                            <p class="font-score font-bold text-2xl mt-1">{{ $currencyService->format((float) $amount, $currencyCode) }}</p>

                            @if ($owner->payout_method)
                                <form method="POST" action="{{ route('admin.payouts.store') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="currency" value="{{ $currencyCode }}">
                                    <button type="submit" class="w-full bg-amber-signal hover:bg-amber-signal-dark text-ink font-semibold py-2 rounded-xl text-sm transition-colors">
                                        {{ __('Request payout') }}
                                    </button>
                                </form>
                            @else
                                <p class="text-xs text-sand-100/60 mt-3">{{ __('Set up a payout destination below first.') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @error('currency')
                <p class="text-red-600 text-sm mt-3">{{ $message }}</p>
            @enderror
        </div>

        {{-- Payout destination --}}
        <div class="mb-10">
            <h2 class="font-display font-bold text-xl mb-4">{{ __('Payout destination') }}</h2>

            <form method="POST" action="{{ route('admin.payouts.destination') }}"
                  x-data="{ method: '{{ old('payout_method', $owner->payout_method ?? 'bank_transfer') }}' }"
                  class="bg-white border border-line rounded-2xl p-5 space-y-4 max-w-lg">
                @csrf
                @method('PUT')

                <div class="flex gap-2">
                    <label class="flex-1 flex items-center justify-center gap-2 text-sm border rounded-xl py-2.5 cursor-pointer transition-colors"
                           :class="method === 'bank_transfer' ? 'border-pitch-600 bg-pitch-50 text-pitch-800' : 'border-line'">
                        <input type="radio" name="payout_method" value="bank_transfer" x-model="method" class="sr-only">
                        {{ __('Bank transfer') }}
                    </label>
                    <label class="flex-1 flex items-center justify-center gap-2 text-sm border rounded-xl py-2.5 cursor-pointer transition-colors"
                           :class="method === 'mobile_money' ? 'border-pitch-600 bg-pitch-50 text-pitch-800' : 'border-line'">
                        <input type="radio" name="payout_method" value="mobile_money" x-model="method" class="sr-only">
                        {{ __('Mobile Money') }}
                    </label>
                </div>

                <div x-show="method === 'bank_transfer'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Bank name') }}</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $owner->payout_details['bank_name'] ?? '') }}"
                               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Account name') }}</label>
                        <input type="text" name="account_name" value="{{ old('account_name', $owner->payout_details['account_name'] ?? '') }}"
                               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Account number') }}</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $owner->payout_details['account_number'] ?? '') }}"
                               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                </div>

                <div x-show="method === 'mobile_money'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Provider') }}</label>
                        <input type="text" name="mobile_provider" placeholder="{{ __('Orange, MTN, Wave...') }}" value="{{ old('mobile_provider', $owner->payout_details['provider'] ?? '') }}"
                               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Phone number') }}</label>
                        <input type="text" name="mobile_number" value="{{ old('mobile_number', $owner->payout_details['number'] ?? '') }}"
                               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                </div>

                @error('bank_name') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                @error('account_name') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                @error('account_number') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                @error('mobile_provider') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror
                @error('mobile_number') <p class="text-red-600 text-xs">{{ $message }}</p> @enderror

                <button type="submit" class="bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold px-5 py-2 rounded-xl text-sm transition-colors">
                    {{ __('Save destination') }}
                </button>
            </form>
        </div>

        {{-- History --}}
        <div>
            <h2 class="font-display font-bold text-xl mb-4">{{ __('Payout history') }}</h2>
            <div class="bg-white border border-line rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-pitch-50 text-ink/60 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Reference') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Requested') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Amount') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Method') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                            @if (auth()->user()->hasRole('finance'))
                                <th class="px-4 py-3"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($payouts as $payout)
                            <tr>
                                <td class="px-4 py-3 font-score text-xs">{{ $payout->reference }}</td>
                                <td class="px-4 py-3 font-score text-ink/70">{{ $payout->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-score font-semibold">{{ $currencyService->format((float) $payout->amount, $payout->currency) }}</td>
                                <td class="px-4 py-3 text-ink/70">{{ $payout->method === 'bank_transfer' ? __('Bank transfer') : __('Mobile Money') }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                                        {{ match($payout->status) {
                                            'paid' => 'bg-pitch-100 text-pitch-800',
                                            'pending', 'processing' => 'bg-amber-signal/20 text-amber-signal-dark',
                                            'failed' => 'bg-red-50 text-red-600',
                                            default => 'bg-ink/10 text-ink/50',
                                        } }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                @if (auth()->user()->hasRole('finance'))
                                    <td class="px-4 py-3 text-right">
                                        @if (in_array($payout->status, ['pending', 'processing']))
                                            <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" class="inline">
                                                @csrf
                                                <button class="text-pitch-700 hover:underline text-xs font-medium">{{ __('Mark paid') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payouts.mark-failed', $payout) }}" class="inline ml-2">
                                                @csrf
                                                <button class="text-red-600 hover:underline text-xs font-medium">{{ __('Mark failed') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-ink/50">{{ __('No payouts yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $payouts->links() }}</div>
        </div>
    </div>

@endsection
