<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'responsable' => $this->responsable,
            'contact' => $this->contact,
            'faculte' => $this->faculte,
            'email' => $this->email,
            'statut' => $this->statut,
            'reservations_count' => $this->whenCounted('reservations'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
