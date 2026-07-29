@php
    $pitch = $pitch ?? null;
    $name = fn ($locale) => old("name_{$locale}", $pitch?->name[$locale] ?? '');
    $description = fn ($locale) => old("description_{$locale}", $pitch?->description[$locale] ?? '');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Name (French)') }}</label>
        <input type="text" name="name_fr" value="{{ $name('fr') }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
        @error('name_fr') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Name (English)') }}</label>
        <input type="text" name="name_en" value="{{ $name('en') }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
        @error('name_en') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Name (Portuguese)') }}</label>
        <input type="text" name="name_pt" value="{{ $name('pt') }}"
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Name (Swahili)') }}</label>
        <input type="text" name="name_sw" value="{{ $name('sw') }}"
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Description (French)') }}</label>
        <textarea name="description_fr" rows="3" class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">{{ $description('fr') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Description (English)') }}</label>
        <textarea name="description_en" rows="3" class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">{{ $description('en') }}</textarea>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Country') }}</label>
        <input type="text" name="country" maxlength="2" placeholder="CI" value="{{ old('country', $pitch->country ?? '') }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm uppercase focus:outline-none focus:border-pitch-500">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('City') }}</label>
        <input type="text" name="city" value="{{ old('city', $pitch->city ?? '') }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium mb-1">{{ __('Address') }}</label>
        <input type="text" name="address" value="{{ old('address', $pitch->address ?? '') }}"
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Surface') }}</label>
        <select name="surface_type" class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
            @foreach (['natural_grass', 'synthetic_turf', 'concrete', 'indoor'] as $type)
                <option value="{{ $type }}" @selected(old('surface_type', $pitch->surface_type ?? 'synthetic_turf') === $type)>
                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Capacity') }}</label>
        <input type="number" name="capacity" min="4" max="22" value="{{ old('capacity', $pitch->capacity ?? 5) }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Price / hour') }}</label>
        <input type="number" step="0.01" name="price_per_hour" value="{{ old('price_per_hour', $pitch->price_per_hour ?? '') }}" required
               class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Currency') }}</label>
        <select name="currency" class="w-full border border-line rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pitch-500">
            @foreach (config('currencies.supported') as $code => $c)
                <option value="{{ $code }}" @selected(old('currency', $pitch->currency ?? config('currencies.default')) === $code)>{{ $code }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="block text-sm font-medium mb-2">{{ __('Amenities') }}</label>
    @php $selectedAmenities = old('amenities', $pitch->amenities ?? []); @endphp
    <div class="flex flex-wrap gap-3">
        @foreach (['lighting', 'parking', 'showers', 'equipment_rental'] as $amenity)
            <label class="flex items-center gap-2 text-sm bg-white border border-line rounded-full px-3 py-1.5">
                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" @checked(in_array($amenity, $selectedAmenities))>
                {{ ucfirst(str_replace('_', ' ', $amenity)) }}
            </label>
        @endforeach
    </div>
</div>

<label class="flex items-center gap-2 text-sm font-medium">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $pitch->is_active ?? true))>
    {{ __('Visible in search results') }}
</label>

@if (! $pitch)
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Photos') }}</label>
        <input type="file" name="images[]" multiple accept="image/*" class="text-sm">
    </div>
@endif
