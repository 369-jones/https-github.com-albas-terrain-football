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
         class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 ticket-edge">

        <button type="button" @click="loginOpen = false"
                class="absolute top-4 right-4 text-ink/40 hover:text-ink transition-colors"
                aria-label="{{ __('Close') }}">
            &times;
        </button>

        <div class="text-center mb-6">
            <img src="{{ asset('images/logo-monochrome.svg') }}" alt="{{ config('app.name') }}" class="w-14 h-14 mx-auto mb-3">
            <h2 class="font-display font-bold text-xl">{{ __('Log in') }}</h2>
            <p class="text-sm text-ink/50 mt-1">{{ config('app.name') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-pitch-50 border border-pitch-100 text-pitch-700 text-sm rounded-xl px-4 py-2.5 mb-4 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus
                       class="w-full border border-line rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Password') }}</label>
                <input type="password" name="password" autocomplete="current-password"
                       class="w-full border border-line rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
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
