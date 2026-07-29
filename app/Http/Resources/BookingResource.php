<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'booking_date' => $this->booking_date?->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_price' => (float) $this->total_price,
            'currency' => $this->currency,
            'notes' => $this->notes,
            'pitch' => [
                'id' => $this->pitch?->id,
                'slug' => $this->pitch?->slug,
                'name' => $this->pitch?->nameFor(),
                'sport' => $this->pitch?->sport,
            ],
            'player' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
