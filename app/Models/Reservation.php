<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'equipe_a_id',
        'equipe_b_id',
        'date_match',
        'creneau',
        'type_match',
        'montant',
        'devise',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_match' => 'date',
        'montant' => 'decimal:2',
    ];

    // Équipe locale
    public function equipeA()
    {
        return $this->belongsTo(Equipe::class, 'equipe_a_id');
    }

    // Équipe visiteur
    public function equipeB()
    {
        return $this->belongsTo(Equipe::class, 'equipe_b_id');
    }

    // Paiement lié
    public function paiement()
    {
        return $this->hasOne(Paiement::class);
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
            'confirme' => '<span class="badge badge-green">Confirmé</span>',
            'en_attente' => '<span class="badge badge-orange">En attente</span>',
            'annule' => '<span class="badge badge-red">Annulé</span>',
            default => '<span class="badge badge-gray">'.e($this->statut).'</span>',
        };
    }
}
