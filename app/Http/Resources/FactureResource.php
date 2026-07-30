<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FactureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero_facture' => $this->numero_facture,
            'montant' => (float) $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'date_emission' => $this->date_emission?->toDateString(),
            'notes' => $this->notes,
            'reservation' => [
                'id' => $this->reservation?->id,
                'equipe_a' => $this->reservation?->equipeA?->nom,
                'equipe_b' => $this->reservation?->equipeB?->nom,
            ],
            'pdf_url' => route('api.v1.factures.pdf', $this->id),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
