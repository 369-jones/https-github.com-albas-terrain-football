@extends('layouts.public')

@section('title', __('Stadium managers'))

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

        @include('admin._nav', ['active' => 'admin.staff.index'])

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-display font-bold text-3xl">{{ __('Stadium managers') }}</h1>
                <p class="text-sm text-ink/50 mt-1">{{ __('Only you can assign who is responsible for each stadium.') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-pitch-100 text-pitch-800 text-sm rounded-xl px-4 py-2.5 mb-6" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-line rounded-2xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-pitch-50 text-ink/60 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium">{{ __('Stadium') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Sport') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('City') }}</th>
                        <th class="px-4 py-3 font-medium">{{ __('Responsible manager') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach ($pitches as $pitch)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $pitch->nameFor() }}</td>
                            <td class="px-4 py-3 text-ink/70"><i class="fa-solid {{ $pitch->sportIcon() }} mr-1.5"></i>{{ __(ucfirst($pitch->sport)) }}</td>
                            <td class="px-4 py-3 text-ink/70">{{ $pitch->city }}</td>
                            <td class="px-4 py-3">
                                @if ($pitch->owner)
                                    <p class="font-medium">{{ $pitch->owner->name }}</p>
                                    <p class="text-ink/50 text-xs">{{ $pitch->owner->email }}</p>
                                @else
                                    <span class="text-ink/40">{{ __('Unassigned') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.staff.edit', $pitch) }}" class="text-pitch-700 hover:underline">{{ __('Reassign') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
