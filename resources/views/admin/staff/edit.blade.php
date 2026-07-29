@extends('layouts.public')

@section('title', __('Assign a manager'))

@section('content')

    <div class="max-w-xl mx-auto px-4 sm:px-6 py-10">

        <a href="{{ route('admin.staff.index') }}" class="text-sm text-ink/60 hover:text-pitch-700">&larr; {{ __('Back to stadium managers') }}</a>

        <h1 class="font-display font-bold text-3xl mt-4">{{ $pitch->nameFor() }}</h1>
        <p class="text-ink/60 mt-1"><i class="fa-solid {{ $pitch->sportIcon() }} mr-1.5"></i>{{ __(ucfirst($pitch->sport)) }} &middot; {{ $pitch->city }}</p>

        <div class="bg-pitch-50 border border-pitch-100 rounded-xl px-4 py-3 mt-6 text-sm">
            <p class="text-ink/50 text-xs uppercase tracking-wide mb-1">{{ __('Currently responsible') }}</p>
            @if ($pitch->owner)
                <p class="font-medium">{{ $pitch->owner->name }} <span class="text-ink/50">— {{ $pitch->owner->email }}</span></p>
            @else
                <p class="text-ink/40">{{ __('No one assigned yet.') }}</p>
            @endif
        </div>

        @if ($errors->any())
            <div class="flex items-start gap-2 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl px-4 py-2.5 mt-6">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.update', $pitch) }}" class="space-y-4 mt-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Manager email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-line rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
                <p class="text-xs text-ink/40 mt-1">{{ __('Use an existing staff email to reassign, or a new one to create their account.') }}</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Full name') }} <span class="text-ink/30">({{ __('only needed for a new account') }})</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-line rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-ink/50 mb-1">{{ __('Password') }} <span class="text-ink/30">({{ __('leave blank to keep an existing account\'s password') }})</span></label>
                <input type="password" name="password" autocomplete="new-password"
                       class="w-full border border-line rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-pitch-500">
            </div>

            <button type="submit"
                    class="w-full bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold py-2.5 rounded-xl text-sm transition-colors">
                {{ __('Assign manager') }}
            </button>
        </form>
    </div>

@endsection
