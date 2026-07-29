@extends('layouts.public')

@section('title', __('Edit pitch'))

@section('content')

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-ink/60 hover:text-pitch-700">&larr; {{ __('Back to dashboard') }}</a>
        <h1 class="font-display font-bold text-3xl mt-4 mb-8">{{ __('Edit pitch') }}</h1>

        <form method="POST" action="{{ route('admin.pitches.update', $pitch) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.pitches._form', ['pitch' => $pitch])

            <button type="submit" class="w-full bg-pitch-800 hover:bg-pitch-900 text-sand-100 font-semibold py-3 rounded-xl transition-colors">
                {{ __('Save changes') }}
            </button>
        </form>

        {{-- Photos --}}
        <div class="mt-10">
            <h2 class="font-display font-bold text-xl mb-4">{{ __('Photos') }}</h2>
            <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach ($pitch->images as $image)
                    <div class="relative aspect-square rounded-xl overflow-hidden border border-line group">
                        <img src="{{ $image->url() }}" class="w-full h-full object-cover">
                        @if ($image->is_primary)
                            <span class="absolute top-1.5 left-1.5 bg-amber-signal text-ink text-[10px] font-bold px-2 py-0.5 rounded-full">{{ __('Cover') }}</span>
                        @else
                            <form method="POST" action="{{ route('admin.pitches.images.primary', [$pitch, $image]) }}" class="absolute top-1.5 left-1.5">
                                @csrf
                                <button class="bg-ink/70 text-sand-100 text-[10px] font-bold px-2 py-0.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">{{ __('Set cover') }}</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.pitches.images.destroy', [$pitch, $image]) }}" class="absolute top-1.5 right-1.5">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white text-[10px] font-bold w-5 h-5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
            <form method="POST" action="{{ route('admin.pitches.update', $pitch) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="file" name="images[]" multiple accept="image/*" class="text-sm">
                <button type="submit" class="mt-2 bg-white border border-line hover:border-pitch-500 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                    {{ __('Upload photos') }}
                </button>
            </form>
        </div>

        {{-- Blocked dates --}}
        <div class="mt-10">
            <h2 class="font-display font-bold text-xl mb-4">{{ __('Blocked dates') }}</h2>
            <p class="text-sm text-ink/60 mb-4">{{ __('Close the pitch off for maintenance or a private event — no bookings will be possible on that date.') }}</p>

            <div class="space-y-2 mb-4">
                @forelse ($pitch->blocks as $block)
                    <div class="flex items-center justify-between bg-white border border-line rounded-xl px-4 py-2.5 text-sm">
                        <span class="font-score">{{ $block->date->format('d/m/Y') }}{{ $block->reason ? ' — '.$block->reason : '' }}</span>
                        <form method="POST" action="{{ route('admin.pitches.blocks.destroy', [$pitch, $block]) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">{{ __('Unblock') }}</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-ink/50">{{ __('No blocked dates.') }}</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.pitches.blocks.store', $pitch) }}" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input type="date" name="date" required min="{{ now()->toDateString() }}" class="border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                <input type="text" name="reason" placeholder="{{ __('Reason (optional)') }}" class="flex-1 border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
                <button type="submit" class="bg-ink text-sand-100 font-medium px-4 py-2 rounded-xl text-sm">{{ __('Block date') }}</button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.pitches.destroy', $pitch) }}" class="mt-10 pt-6 border-t border-line" onsubmit="return confirm('{{ __('Delete this pitch permanently?') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 text-sm font-medium hover:underline">{{ __('Delete this pitch') }}</button>
        </form>
    </div>

@endsection
