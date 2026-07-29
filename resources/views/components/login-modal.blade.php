{{-- Backdrop + dialog. Parent (<body>) owns the `loginOpen` Alpine state so both
     the header trigger and this modal, wherever it's included, share it. --}}
<div x-show="loginOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="loginOpen = false">

    <div x-show="loginOpen" x-transition.opacity
         class="absolute inset-0 bg-ink/60"
         @click="loginOpen = false"></div>

    <div x-show="loginOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden grid grid-cols-1 sm:grid-cols-2">

        <button type="button" @click="loginOpen = false"
                class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-ink/5 text-ink/40 hover:text-ink hover:bg-ink/10 transition-colors"
                aria-label="{{ __('Close') }}">
            <i class="fa-solid fa-xmark"></i>
        </button>

        {{-- Brand panel — hidden on small screens to keep the modal compact there --}}
        <div class="hidden sm:flex flex-col justify-between bg-pitch-900 text-sand-100 p-8 relative overflow-hidden">
            <svg class="absolute -right-16 -bottom-16 w-72 h-72 opacity-[0.08] pointer-events-none" viewBox="0 0 400 400" fill="none">
                <rect x="20" y="20" width="360" height="360" rx="8" stroke="white" stroke-width="3"/>
                <circle cx="200" cy="200" r="60" stroke="white" stroke-width="3"/>
                <line x1="200" y1="20" x2="200" y2="380" stroke="white" stroke-width="3"/>
            </svg>

            <div class="relative z-10">
                <img src="{{ asset('images/logo-icon.svg') }}" alt="{{ config('app.name') }}" class="w-14 h-14 mb-6">
                <h2 class="font-display font-bold text-2xl leading-tight">{{ __('Welcome back to :name', ['name' => config('app.name')]) }}</h2>
                <p class="text-sand-100/70 text-sm mt-2">{{ __('Book football pitches, basketball and volleyball courts in seconds.') }}</p>
            </div>

            <ul class="relative z-10 space-y-3 text-sm text-sand-100/80">
                <li class="flex items-center gap-2"><i class="fa-solid fa-calendar-check text-amber-signal"></i> {{ __('Track and manage your bookings') }}</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-star text-amber-signal"></i> {{ __('Rate the venues you play at') }}</li>
                <li class="flex items-center gap-2"><i class="fa-solid fa-sack-dollar text-amber-signal"></i> {{ __('Owners: track earnings and payouts') }}</li>
            </ul>
        </div>

        {{-- Form panel --}}
        <div class="p-8 flex flex-col justify-center">
            <div class="sm:hidden text-center mb-6">
                <img src="{{ asset('images/logo-monochrome.svg') }}" alt="{{ config('app.name') }}" class="w-12 h-12 mx-auto mb-2">
            </div>

            <h2 class="font-display font-bold text-xl">{{ __('Log in') }}</h2>
            <p class="text-sm text-ink/50 mt-1 mb-6">{{ config('app.name') }}</p>

            @if ($errors->any())
                <div class="flex items-start gap-2 bg-pitch-50 border border-pitch-100 text-pitch-700 text-sm rounded-xl px-4 py-2.5 mb-4">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Email') }}</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-ink/30 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus
                               class="w-full border border-line rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Password') }}</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-ink/30 text-sm"></i>
                        <input type="password" name="password" autocomplete="current-password"
                               class="w-full border border-line rounded-xl pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink/60">
                    <input type="checkbox" name="remember">
                    {{ __('Remember me') }}
                </label>
                <button type="submit"
                        class="w-full bg-pitch-700 hover:bg-pitch-800 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                    {{ __('Log in') }}
                </button>
            </form>
        </div>
    </div>
</div>
