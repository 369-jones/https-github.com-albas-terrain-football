<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $validated = $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date|after_or_equal:today',
            'creneau' => 'required|in:'.implode(',', Reservation::CRENEAUX),
            'type_match' => 'required|in:'.implode(',', Reservation::TYPES_MATCH),
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

        try {
            Reservation::bookSlot($validated);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

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
        $validated = $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date',
            'creneau' => 'required|in:'.implode(',', Reservation::CRENEAUX),
            'type_match' => 'required|in:'.implode(',', Reservation::TYPES_MATCH),
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation mise à jour avec succès !');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->cancel();

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation annulée avec succès !');
    }
}
