<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pitch;
use App\Models\PitchBlock;
use App\Models\PitchImage;
use App\Services\CurrencyService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PitchController extends Controller
{
    private const OPEN_HOUR = 8;
    private const CLOSE_HOUR = 23;

    public const AMENITIES = ['lighting', 'parking', 'showers', 'equipment_rental'];
    public const SURFACE_TYPES = ['natural_grass', 'synthetic_turf', 'concrete', 'indoor'];

    public function index(Request $request, CurrencyService $currencyService): View
    {
        $currency = session('currency', config('currencies.default'));

        $query = Pitch::query()
            ->with('images')
            ->withAvg('reviews', 'rating')
            ->where('is_active', true)
            ->when($request->filled('q'), function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $term = $request->string('q');
                    $qq->where('city', 'like', "%{$term}%")
                       ->orWhere('address', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('surface'), function ($q) use ($request) {
                $surfaces = array_intersect((array) $request->input('surface'), self::SURFACE_TYPES);
                if ($surfaces) {
                    $q->whereIn('surface_type', $surfaces);
                }
            })
            ->when($request->filled('capacity'), fn ($q) => $q->where('capacity', $request->integer('capacity')))
            ->when($request->filled('amenities'), function ($q) use ($request) {
                foreach (array_intersect((array) $request->input('amenities'), self::AMENITIES) as $amenity) {
                    $q->whereJsonContains('amenities', $amenity);
                }
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->string('date');
                $q->whereDoesntHave('blocks', fn ($b) => $b->whereDate('date', $date));
            });

        $pitches = $query->get();

        if ($request->filled('date')) {
            $date = Carbon::parse($request->string('date'));
            $pitches = $pitches->filter(fn (Pitch $pitch) => $this->hasAnyAvailability($pitch, $date));
        }

        // Prices are stored per-pitch in whatever currency the owner set — the comparison/sort
        // only makes sense once every pitch is converted into the shopper's selected currency,
        // which requires PHP (exchange rates aren't known to raw SQL), hence filtering here
        // instead of in the query builder above.
        $pitches = $pitches->map(function (Pitch $pitch) use ($currencyService, $currency) {
            $pitch->display_price = $currencyService->convert((float) $pitch->price_per_hour, $pitch->currency, $currency);

            return $pitch;
        });

        if ($request->filled('min_price')) {
            $min = (float) $request->input('min_price');
            $pitches = $pitches->filter(fn (Pitch $pitch) => $pitch->display_price >= $min);
        }
        if ($request->filled('max_price')) {
            $max = (float) $request->input('max_price');
            $pitches = $pitches->filter(fn (Pitch $pitch) => $pitch->display_price <= $max);
        }

        $pitches = (match ($request->input('sort')) {
            'price_asc' => $pitches->sortBy('display_price'),
            'price_desc' => $pitches->sortByDesc('display_price'),
            'rating' => $pitches->sortByDesc('reviews_avg_rating'),
            default => $pitches->sortByDesc('created_at'),
        })->values();

        $perPage = 9;
        $page = $request->integer('page', 1);
        $pitches = new LengthAwarePaginator(
            $pitches->forPage($page, $perPage)->values(),
            $pitches->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pitches.index', compact('pitches', 'currency'));
    }

    /**
     * Whether the pitch has at least one open hourly slot on the given date —
     * used by the search filter to hide pitches that are fully booked that day.
     */
    private function hasAnyAvailability(Pitch $pitch, Carbon $date): bool
    {
        foreach ($this->buildSlots($pitch, $date) as $slot) {
            if ($slot['available']) {
                return true;
            }
        }

        return false;
    }

    public function show(Request $request, Pitch $pitch): View
    {
        $pitch->load(['images', 'owner', 'reviews.user']);

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->string('date'))
            : Carbon::today();

        // Only allow browsing today through the next 13 days.
        $dateOptions = collect(range(0, 13))->map(fn ($i) => Carbon::today()->addDays($i));

        $slots = $this->buildSlots($pitch, $selectedDate);

        $reviewsAvg = $pitch->reviews->avg('rating');

        $eligibleBookingToReview = null;
        if ($request->user()) {
            $eligibleBookingToReview = Booking::where('pitch_id', $pitch->id)
                ->where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->latest('booking_date')
                ->first();
        }

        return view('pitches.show', compact(
            'pitch', 'selectedDate', 'dateOptions', 'slots', 'reviewsAvg', 'eligibleBookingToReview'
        ));
    }

    /**
     * Build the day's hourly slots (e.g. pitch open 08:00–23:00) and mark which
     * ones are already booked, so the view never has to guess availability.
     */
    private function buildSlots(Pitch $pitch, Carbon $date): array
    {
        $openHour = self::OPEN_HOUR;
        $closeHour = self::CLOSE_HOUR;

        $isBlocked = $pitch->blocks()->whereDate('date', $date->toDateString())->exists();

        $bookedRanges = Booking::query()
            ->where('pitch_id', $pitch->id)
            ->whereDate('booking_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['start_time', 'end_time']);

        $slots = [];
        for ($hour = $openHour; $hour < $closeHour; $hour++) {
            $start = sprintf('%02d:00', $hour);
            $end = sprintf('%02d:00', $hour + 1);

            $isPast = $date->isToday() && $hour <= now()->hour;

            $isBooked = $bookedRanges->contains(
                fn ($b) => $b->start_time < $end && $b->end_time > $start
            );

            $slots[] = [
                'start' => $start,
                'end' => $end,
                'available' => ! $isPast && ! $isBooked && ! $isBlocked,
            ];
        }

        return $slots;
    }

    public function dashboard(Request $request): View
    {
        $pitches = Pitch::where('owner_id', $request->user()->id)->get();
        $pitchIds = $pitches->pluck('id');

        $upcomingBookings = Booking::whereIn('pitch_id', $pitchIds)
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['user', 'pitch'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(20)
            ->get();

        $stats = [
            'week_bookings' => Booking::whereIn('pitch_id', $pitchIds)
                ->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'month_revenue' => Booking::whereIn('pitch_id', $pitchIds)
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_price'),
            'currency' => $pitches->first()->currency ?? config('currencies.default'),
            'occupancy' => 0, // wire up to a real calc once you have booking volume to measure against
        ];

        return view('admin.dashboard', compact('pitches', 'upcomingBookings', 'stats'));
    }

    public function create(): View
    {
        return view('admin.pitches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePitch($request);

        $pitch = Pitch::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'slug' => $this->uniqueSlug($validated['name']['en'] ?? $validated['name']['fr']),
        ]);

        $this->storeUploadedImages($request, $pitch);

        return redirect()->route('admin.pitches.edit', $pitch)->with('success', __('Pitch created.'));
    }

    public function edit(Request $request, Pitch $pitch): View
    {
        $this->authorizeOwnerOf($pitch);
        $pitch->load(['images', 'blocks' => fn ($q) => $q->orderBy('date')]);

        return view('admin.pitches.edit', compact('pitch'));
    }

    public function update(Request $request, Pitch $pitch): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);

        $validated = $this->validatePitch($request);
        $pitch->update($validated);

        $this->storeUploadedImages($request, $pitch);

        return redirect()->route('admin.pitches.edit', $pitch)->with('success', __('Pitch updated.'));
    }

    public function destroy(Request $request, Pitch $pitch): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);
        $pitch->delete();

        return redirect()->route('admin.dashboard')->with('success', __('Pitch removed.'));
    }

    public function destroyImage(Request $request, Pitch $pitch, PitchImage $image): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);
        abort_unless($image->pitch_id === $pitch->id, 404);

        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $pitch->images()->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', __('Photo removed.'));
    }

    public function setPrimaryImage(Request $request, Pitch $pitch, PitchImage $image): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);
        abort_unless($image->pitch_id === $pitch->id, 404);

        $pitch->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', __('Cover photo updated.'));
    }

    public function storeBlock(Request $request, Pitch $pitch): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        PitchBlock::firstOrCreate(
            ['pitch_id' => $pitch->id, 'date' => $validated['date']],
            ['reason' => $validated['reason'] ?? null]
        );

        return back()->with('success', __('Date blocked.'));
    }

    public function destroyBlock(Request $request, Pitch $pitch, PitchBlock $block): RedirectResponse
    {
        $this->authorizeOwnerOf($pitch);
        abort_unless($block->pitch_id === $pitch->id, 404);

        $block->delete();

        return back()->with('success', __('Date unblocked.'));
    }

    private function validatePitch(Request $request): array
    {
        $validated = $request->validate([
            'name_fr' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'name_pt' => ['nullable', 'string', 'max:120'],
            'name_sw' => ['nullable', 'string', 'max:120'],
            'description_fr' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'country' => ['required', 'string', 'size:2'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'surface_type' => ['required', 'in:natural_grass,synthetic_turf,concrete,indoor'],
            'capacity' => ['required', 'integer', 'min:4', 'max:22'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        return [
            'name' => array_filter([
                'fr' => $validated['name_fr'],
                'en' => $validated['name_en'],
                'pt' => $validated['name_pt'] ?? null,
                'sw' => $validated['name_sw'] ?? null,
            ]),
            'description' => array_filter([
                'fr' => $validated['description_fr'] ?? null,
                'en' => $validated['description_en'] ?? null,
            ]),
            'country' => strtoupper($validated['country']),
            'city' => $validated['city'],
            'address' => $validated['address'] ?? null,
            'surface_type' => $validated['surface_type'],
            'capacity' => $validated['capacity'],
            'amenities' => $validated['amenities'] ?? [],
            'price_per_hour' => $validated['price_per_hour'],
            'currency' => strtoupper($validated['currency']),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function storeUploadedImages(Request $request, Pitch $pitch): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasPrimaryAlready = $pitch->images()->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store("pitches/{$pitch->id}", 'public');

            PitchImage::create([
                'pitch_id' => $pitch->id,
                'path' => $path,
                'is_primary' => ! $hasPrimaryAlready && $index === 0,
                'sort_order' => $pitch->images()->count(),
            ]);
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Pitch::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function authorizeOwnerOf(Pitch $pitch): void
    {
        abort_unless($pitch->owner_id === request()->user()?->id, 403);
    }
}
