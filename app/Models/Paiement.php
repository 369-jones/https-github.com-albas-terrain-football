<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
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
}
