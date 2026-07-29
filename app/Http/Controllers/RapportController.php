<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Paiement;
use App\Models\Reservation;

class RapportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_reservations' => Reservation::count(),
            'confirmes' => Reservation::where('statut', 'confirme')->count(),
            'en_attente' => Reservation::where('statut', 'en_attente')->count(),
            'annules' => Reservation::where('statut', 'annule')->count(),
            'revenus_total' => Paiement::where('statut', 'paye')->sum('montant_paye'),
            'revenus_mois' => Paiement::where('statut', 'paye')
                                        ->whereMonth('date_paiement', now()->month)
                                        ->sum('montant_paye'),
            'impayes_montant' => Paiement::where('statut', 'impaye')->sum('montant_du'),
            'total_equipes' => Equipe::count(),
        ];

        $paiements_par_mode = Paiement::where('statut', 'paye')
            ->selectRaw('mode_paiement, COUNT(*) as total, SUM(montant_paye) as montant')
            ->groupBy('mode_paiement')
            ->get();

        $reservations_par_mois = Reservation::selectRaw('MONTH(date_match) as mois, COUNT(*) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        return view('rapports.index', compact('stats', 'paiements_par_mode', 'reservations_par_mois'));
    }
}
