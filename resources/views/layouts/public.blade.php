<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Book a football pitch near you'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col font-body text-ink bg-sand">

    <header class="bg-pitch-900 text-sand-100 sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-display font-bold text-lg tracking-tight flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-signal"></span>
                {{ config('app.name') }}
            </a>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                <a href="{{ route('home') }}" class="hover:text-amber-signal transition-colors">{{ __('Find a pitch') }}</a>
                @auth
                    <a href="{{ route('bookings.mine') }}" class="hover:text-amber-signal transition-colors">{{ __('My bookings') }}</a>
                    @if(auth()->user()->hasRole('owner'))
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-amber-signal transition-colors">{{ __('Owner dashboard') }}</a>
                    @endif
                @endauth
            </nav>

            <div class="flex items-center gap-3">
                <x-currency-switcher />
                <x-language-switcher />

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm font-medium hover:text-amber-signal transition-colors">{{ __('Log out') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm font-semibold bg-amber-signal text-ink px-4 py-2 rounded-full hover:bg-amber-signal-dark transition-colors">
                        {{ __('Log in') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('success'))
        <div class="bg-pitch-100 text-pitch-800 text-sm text-center py-2 px-4" role="status">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-700 text-sm text-center py-2 px-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-pitch-950 text-sand-100/70 text-sm mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row justify-between gap-4">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
            <p>{{ __('Prices shown include all fees.') }}</p>
        </div>
    </footer>

</body>
</html>
