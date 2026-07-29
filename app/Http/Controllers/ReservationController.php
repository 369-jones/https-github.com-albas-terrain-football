<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['equipeA', 'equipeB', 'paiement'])
            ->latest()
            ->get();

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $equipes = Equipe::where('statut', 'actif')->orderBy('nom')->get();

        return view('reservations.create', compact('equipes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date|after_or_equal:today',
            'creneau' => 'required',
            'type_match' => 'required',
            'montant' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'equipe_a_id.required' => 'L\'équipe A est obligatoire.',
            'equipe_b_id.required' => 'L\'équipe B est obligatoire.',
            'equipe_b_id.different' => 'Les deux équipes doivent être différentes.',
            'date_match.required' => 'La date est obligatoire.',
            'date_match.after_or_equal' => 'La date doit être aujourd\'hui ou dans le futur.',
            'creneau.required' => 'Le créneau est obligatoire.',
            'montant.required' => 'Le montant est obligatoire.',
        ]);

        // Vérifier conflit de créneau
        $conflit = Reservation::where('date_match', $request->date_match)
            ->where('creneau', $request->creneau)
            ->where('statut', '!=', 'annule')
            ->exists();

        if ($conflit) {
            return back()->withErrors([
                'creneau' => 'Ce créneau est déjà réservé pour cette date !',
            ])->withInput();
        }

        $reservation = Reservation::create($request->all());

        // Récupérer les noms des équipes
        $equipeA = \App\Models\Equipe::find($request->equipe_a_id);
        $equipeB = \App\Models\Equipe::find($request->equipe_b_id);

        NotificationService::nouvelleReservation(
            $equipeA->nom,
            $equipeB->nom,
            \Carbon\Carbon::parse($request->date_match)->format('d/m/Y')
        );

        NotificationService::nouvelleReservation(
            $request->input('equipe_a_id'),
            $request->input('equipe_b_id'),
            $request->input('date_match')
        );

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation créée avec succès !');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('equipeA', 'equipeB', 'paiement');

        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $equipes = Equipe::where('statut', 'actif')->orderBy('nom')->get();

        return view('reservations.edit', compact('reservation', 'equipes'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date',
            'creneau' => 'required',
            'type_match' => 'required',
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $reservation->update($request->all());

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation mise à jour avec succès !');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update(['statut' => 'annule']);
        $reservation->load('equipeA', 'equipeB');
        NotificationService::reservationAnnulee(
            $reservation->equipeA->nom,
            $reservation->equipeB->nom
        );

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation annulée avec succès !');
    }
}
