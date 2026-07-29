<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_match' => $this->date_match?->toDateString(),
            'creneau' => $this->creneau,
            'type_match' => $this->type_match,
            'montant' => (float) $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'notes' => $this->notes,
            'equipe_a' => [
                'id' => $this->equipeA?->id,
                'nom' => $this->equipeA?->nom,
            ],
            'equipe_b' => [
                'id' => $this->equipeB?->id,
                'nom' => $this->equipeB?->nom,
            ],
            'paiement_statut' => $this->whenLoaded('paiement', fn () => $this->paiement?->statut),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
