<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PitchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'sport' => $this->sport,
            'name' => $this->nameFor(),
            'city' => $this->city,
            'country' => $this->country,
            'address' => $this->address,
            'surface_type' => $this->surface_type,
            'capacity' => $this->capacity,
            'amenities' => $this->amenities,
            'price_per_hour' => (float) $this->price_per_hour,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'owner' => [
                'id' => $this->owner?->id,
                'name' => $this->owner?->name,
                'email' => $this->owner?->email,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
