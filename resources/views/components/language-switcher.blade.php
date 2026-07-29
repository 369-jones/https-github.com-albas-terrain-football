@php
    $current = app()->getLocale();
    $languages = config('locales.supported');
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button @click="open = !open"
            type="button"
            class="flex items-center gap-1 text-sm font-medium hover:text-amber-signal transition-colors"
            :aria-expanded="open"
            aria-haspopup="true">
        <span>{{ $languages[$current]['flag'] ?? '' }}</span>
        <span class="uppercase">{{ $current }}</span>
    </button>

    <div x-show="open" x-cloak
         class="absolute right-0 mt-2 w-40 bg-white text-ink rounded-lg shadow-lg border border-line overflow-hidden">
        @foreach ($languages as $code => $lang)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
               class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-pitch-50 {{ $code === $current ? 'font-semibold text-pitch-700' : '' }}">
                <span>{{ $lang['flag'] }}</span> {{ $lang['name'] }}
            </a>
        @endforeach
    </div>
</div>
