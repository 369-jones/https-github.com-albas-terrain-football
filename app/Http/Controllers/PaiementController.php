<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index()
    {
        $paiements = Paiement::with(['reservation.equipeA', 'reservation.equipeB'])
            ->latest()
            ->get();

        return view('paiements.index', compact('paiements'));
    }

    public function create()
    {
        $reservations = Reservation::with(['equipeA', 'equipeB'])
            ->where('statut', '!=', 'annule')
            ->whereDoesntHave('paiement', fn ($q) => $q->where('statut', 'paye'))
            ->orderBy('date_match')
            ->get();

        return view('paiements.create', compact('reservations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant_paye' => 'required|numeric|min:1',
            'mode_paiement' => 'required|in:'.implode(',', Paiement::MODES_PAIEMENT),
            'date_paiement' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ], [
            'reservation_id.required' => 'La réservation est obligatoire.',
            'montant_paye.required' => 'Le montant est obligatoire.',
            'montant_paye.min' => 'Le montant doit être supérieur à 0.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'date_paiement.required' => 'La date de paiement est obligatoire.',
        ]);

        Paiement::record($validated);

        return redirect()->route('paiements.index')
           ->with('success', 'Paiement enregistré avec succès !');
    }

    public function show(Paiement $paiement)
    {
        $paiement->load('reservation.equipeA', 'reservation.equipeB', 'facture');

        return view('paiements.show', compact('paiement'));
    }

    public function recu(Paiement $paiement)
    {
        $paiement->load('reservation.equipeA', 'reservation.equipeB', 'facture');

        if ($paiement->facture) {
            return redirect()->route('factures.pdf', $paiement->facture);
        }

        return view('paiements.recu', compact('paiement'));
    }

    public function edit(Paiement $paiement)
    {
        return redirect()->route('paiements.index');
    }

    public function update(Request $request, Paiement $paiement)
    {
        return redirect()->route('paiements.index');
    }

    public function destroy(Paiement $paiement)
    {
        return redirect()->route('paiements.index');
    }
}
