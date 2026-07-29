@php
    $current = session('currency', config('currencies.default'));
    $currencies = config('currencies.supported');
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open"
            type="button"
            class="flex items-center gap-1 text-sm font-medium hover:text-amber-signal transition-colors"
            :aria-expanded="open"
            aria-haspopup="true">
        <span class="font-score">{{ $currencies[$current]['symbol'] ?? $current }}</span>
        <span class="uppercase">{{ $current }}</span>
    </button>

    <div x-show="open" x-cloak
         class="absolute right-0 mt-2 w-56 max-h-72 overflow-y-auto bg-white text-ink rounded-lg shadow-lg border border-line">
        @foreach ($currencies as $code => $c)
            <a href="{{ request()->fullUrlWithQuery(['currency' => $code]) }}"
               class="flex items-center justify-between px-3 py-2 text-sm hover:bg-pitch-50 {{ $code === $current ? 'font-semibold text-pitch-700' : '' }}">
                <span>{{ $c['name'] }}</span>
                <span class="font-score text-xs text-ink/60">{{ $code }}</span>
            </a>
        @endforeach
    </div>
</div>
