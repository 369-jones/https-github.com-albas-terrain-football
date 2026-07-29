<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'paiement_id',
        'reservation_id',
        'numero_facture',
        'montant',
        'devise',
        'statut',
        'date_emission',
        'notes',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'montant' => 'decimal:2',
    ];

    // Paiement lié
    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    // Réservation liée
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Badge statut
    public function statutBadge()
    {
        return match ($this->statut) {
            'payee' => '<span class="badge badge-green">Payée</span>',
            'emise' => '<span class="badge badge-blue">Émise</span>',
            'annulee' => '<span class="badge badge-red">Annulée</span>',
            default => '<span class="badge badge-gray">'.e($this->statut).'</span>',
        };
    }
}
