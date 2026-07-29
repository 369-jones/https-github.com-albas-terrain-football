<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Services\NotificationService;

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
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant_paye' => 'required|numeric|min:1',
            'mode_paiement' => 'required',
            'date_paiement' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ], [
            'reservation_id.required' => 'La réservation est obligatoire.',
            'montant_paye.required' => 'Le montant est obligatoire.',
            'montant_paye.min' => 'Le montant doit être supérieur à 0.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'date_paiement.required' => 'La date de paiement est obligatoire.',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);
        $devise = $reservation->devise; // ← devise héritée de la réservation
        $montant_paye = min($request->montant_paye, $reservation->montant);
        $statut = $montant_paye >= $reservation->montant ? 'paye' : 'partiel';

        $paiement = Paiement::updateOrCreate(
            ['reservation_id' => $request->reservation_id],
            [
                'montant_du' => $reservation->montant,
                'montant_paye' => $montant_paye,
                'mode_paiement' => $request->mode_paiement,
                'date_paiement' => $request->date_paiement,
                'reference' => $request->reference,
                'statut' => $statut,
                'devise' => $devise, // ← propagée automatiquement
            ]
        );

        if ($statut === 'paye') {
            $reservation->update(['statut' => 'confirme']);

            $numero = 'FACT-'.str_pad(Facture::count() + 1, 4, '0', STR_PAD_LEFT);
            Facture::create([
                'paiement_id' => $paiement->id,
                'reservation_id' => $reservation->id,
                'numero_facture' => $numero,
                'montant' => $montant_paye,
                'statut' => 'payee',
                'date_emission' => now()->toDateString(),
                'devise' => $devise, // ← propagée automatiquement
            ]);
        }

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
