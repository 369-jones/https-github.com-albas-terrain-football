<?php

namespace App\Models;

use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Reservation extends Model
{
    // Mirrors the enum columns in the reservations table exactly — kept here so
    // validation (web and API) can reference one source of truth instead of each
    // hardcoding the list and risking drift from what the DB actually accepts.
    public const CRENEAUX = ['08h00-10h00', '10h00-12h00', '14h00-16h00', '16h00-18h00', '18h00-20h00'];

    public const TYPES_MATCH = ['Match amical', 'Championnat universitaire', 'Coupe interfacultés', 'Tournoi'];

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

    /**
     * Create a reservation after checking the date/creneau isn't already taken, and fire
     * the "new reservation" notification exactly once with the teams' actual names. Shared
     * by the web and API controllers so they can't drift into notifying twice (as the web
     * controller used to, with a second call that passed raw team IDs where names belonged).
     *
     * @throws ValidationException if the slot is already booked.
     */
    public static function bookSlot(array $data): self
    {
        $conflict = static::where('date_match', $data['date_match'])
            ->where('creneau', $data['creneau'])
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'creneau' => 'Ce créneau est déjà réservé pour cette date !',
            ]);
        }

        // Reload so DB-level column defaults (statut, devise) are reflected on the
        // returned instance instead of the in-memory nulls from before the insert.
        $reservation = static::create($data)->fresh();

        $equipeA = Equipe::find($data['equipe_a_id']);
        $equipeB = Equipe::find($data['equipe_b_id']);

        NotificationService::nouvelleReservation(
            $equipeA->nom,
            $equipeB->nom,
            Carbon::parse($data['date_match'])->format('d/m/Y')
        );

        return $reservation;
    }

    public function cancel(): void
    {
        $this->update(['statut' => 'annule']);
        $this->load('equipeA', 'equipeB');

        NotificationService::reservationAnnulee($this->equipeA->nom, $this->equipeB->nom);
    }
}
