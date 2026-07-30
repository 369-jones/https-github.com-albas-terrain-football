<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaiementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant_du' => (float) $this->montant_du,
            'montant_paye' => (float) $this->montant_paye,
            'reste_a_payer' => (float) $this->resteAPayer(),
            'mode_paiement' => $this->mode_paiement,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'date_paiement' => $this->date_paiement?->toDateString(),
            'reference' => $this->reference,
            'reservation' => [
                'id' => $this->reservation?->id,
                'equipe_a' => $this->reservation?->equipeA?->nom,
                'equipe_b' => $this->reservation?->equipeB?->nom,
                'date_match' => $this->reservation?->date_match?->toDateString(),
            ],
            'facture_numero' => $this->whenLoaded('facture', fn () => $this->facture?->numero_facture),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
