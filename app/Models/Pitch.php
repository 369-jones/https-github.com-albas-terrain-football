<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pitch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id', 'slug', 'sport', 'name', 'description', 'country', 'city', 'address',
        'latitude', 'longitude', 'surface_type', 'capacity', 'amenities',
        'price_per_hour', 'currency', 'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'amenities' => 'array',
        'price_per_hour' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Get the pitch name in the given locale, falling back to French then English.
     */
    public function nameFor(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $names = $this->name ?? [];

        return $names[$locale] ?? $names['fr'] ?? $names['en'] ?? reset($names) ?: '';
    }

    public function descriptionFor(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $descriptions = $this->description ?? [];

        return $descriptions[$locale] ?? $descriptions['fr'] ?? $descriptions['en'] ?? null;
    }

    public function sportIcon(): string
    {
        return match ($this->sport) {
            'basketball' => 'fa-basketball',
            'volleyball' => 'fa-volleyball',
            default => 'fa-futbol',
        };
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PitchImage::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PitchBlock::class);
    }
}
