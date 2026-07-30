<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    // Mirrors the mode_paiement enum column exactly, same reasoning as
    // Reservation::CRENEAUX/TYPES_MATCH — one source of truth for validation.
    public const MODES_PAIEMENT = ['Espèces', 'Mobile Money', 'Virement', 'Chèque'];

    protected $fillable = [
        'reservation_id',
        'montant_du',
        'montant_paye',
        'mode_paiement',
        'devise',
        'statut',
        'date_paiement',
        'reference',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant_du' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    // Réservation liée
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Facture liée
    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    // Badge statut
    public function statutBadge()
    {
        return match ($this->statut) {
            'paye' => '<span class="badge badge-green">Payé</span>',
            'partiel' => '<span class="badge badge-orange">Partiel</span>',
            'impaye' => '<span class="badge badge-red">Impayé</span>',
            default => '<span class="badge badge-gray">'.e($this->statut).'</span>',
        };
    }

    // Reste à payer
    public function resteAPayer()
    {
        return max(0, $this->montant_du - $this->montant_paye);
    }

    /**
     * Record a payment against a reservation, capping it at the amount owed, and — once
     * fully paid — confirm the reservation and issue its invoice. Shared by the web and
     * API controllers instead of duplicated, since this is where money and invoice numbers
     * get generated.
     *
     * Uses firstOrNew keyed on paiement_id rather than Facture::create() so recording a
     * correction against an already-fully-paid reservation updates the existing invoice
     * instead of minting a second one with a new number for the same payment.
     */
    public static function record(array $data): self
    {
        $reservation = Reservation::findOrFail($data['reservation_id']);
        $devise = $reservation->devise;
        $montantPaye = min((float) $data['montant_paye'], (float) $reservation->montant);
        $statut = $montantPaye >= (float) $reservation->montant ? 'paye' : 'partiel';

        $paiement = static::updateOrCreate(
            ['reservation_id' => $data['reservation_id']],
            [
                'montant_du' => $reservation->montant,
                'montant_paye' => $montantPaye,
                'mode_paiement' => $data['mode_paiement'],
                'date_paiement' => $data['date_paiement'],
                'reference' => $data['reference'] ?? null,
                'statut' => $statut,
                'devise' => $devise,
            ]
        );

        if ($statut === 'paye') {
            $reservation->update(['statut' => 'confirme']);

            $facture = Facture::firstOrNew(['paiement_id' => $paiement->id]);
            if (! $facture->exists) {
                $facture->reservation_id = $reservation->id;
                $facture->numero_facture = 'FACT-'.str_pad(Facture::count() + 1, 4, '0', STR_PAD_LEFT);
                $facture->statut = 'payee';
                $facture->date_emission = now()->toDateString();
            }
            $facture->montant = $montantPaye;
            $facture->devise = $devise;
            $facture->save();
        }

        return $paiement->fresh();
    }
}
