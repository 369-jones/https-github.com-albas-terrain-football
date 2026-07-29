<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    protected $fillable = [
        'nom',
        'responsable',
        'contact',
        'faculte',
        'email',
        'statut',
    ];

    // Une équipe peut avoir plusieurs réservations en tant qu'équipe A
    public function reservationsA()
    {
        return $this->hasMany(Reservation::class, 'equipe_a_id');
    }

    // Une équipe peut avoir plusieurs réservations en tant qu'équipe B
    public function reservationsB()
    {
        return $this->hasMany(Reservation::class, 'equipe_b_id');
    }

    // Toutes les réservations de l'équipe
    // Pour withCount dans le controller
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'equipe_a_id')
                    ->orWhere('equipe_b_id', $this->id);
    }
}
