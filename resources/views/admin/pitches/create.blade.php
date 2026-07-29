@extends('layouts.public')

@section('title', __('Add a pitch'))

@section('content')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-ink/60 hover:text-pitch-700">&larr; {{ __('Back to dashboard') }}</a>
        <h1 class="font-display font-bold text-3xl mt-4 mb-8">{{ __('Add a pitch') }}</h1>

        <form method="POST" action="{{ route('admin.pitches.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.pitches._form')

            <button type="submit" class="w-full bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold py-3 rounded-xl transition-colors">
                {{ __('Create pitch') }}
            </button>
        </form>
    </div>

@endsection
