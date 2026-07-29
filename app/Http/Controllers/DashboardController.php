<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── STATS PRINCIPALES ────────────────────────
        $stats = [
            'total_reservations' => Reservation::count(),
            'reservations_mois' => Reservation::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
            'en_attente' => Reservation::where('statut', 'en_attente')->count(),
            'confirme' => Reservation::where('statut', 'confirme')->count(),
            'annule' => Reservation::where('statut', 'annule')->count(),
            'total_equipes' => Equipe::where('statut', 'actif')->count(),
            'total_factures' => Facture::count(),
            'revenus_total' => Paiement::where('statut', 'paye')->sum('montant_paye'),
            'revenus_mois' => Paiement::where('statut', 'paye')
                                        ->whereMonth('date_paiement', now()->month)
                                        ->whereYear('date_paiement', now()->year)
                                        ->sum('montant_paye'),
            'impayes_count' => Paiement::where('statut', 'impaye')->count(),
            'impayes_montant' => Paiement::where('statut', 'impaye')->sum('montant_du'),
            'partiels_count' => Paiement::where('statut', 'partiel')->count(),
        ];

        // ── PROCHAINES RÉSERVATIONS ──────────────────
        $prochaines_reservations = Reservation::with(['equipeA', 'equipeB', 'paiement'])
            ->where('date_match', '>=', now()->toDateString())
            ->where('statut', '!=', 'annule')
            ->orderBy('date_match')
            ->limit(6)
            ->get();

        // ── DERNIERS PAIEMENTS ───────────────────────
        $derniers_paiements = Paiement::with([
            'reservation.equipeA',
            'reservation.equipeB',
        ])
            ->latest()
            ->limit(6)
            ->get();

        // ── RÉSERVATIONS PAR JOUR (7 derniers jours) ─
        $reservations_semaine = Reservation::select(
            DB::raw('DATE(created_at) as jour'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('created_at', [now()->subDays(6), now()])
            ->groupBy('jour')
            ->orderBy('jour')
            ->get()
            ->keyBy('jour');

        // ── REVENUS PAR MOIS (6 derniers mois) ───────
        $revenus_mois = Paiement::select(
            DB::raw('MONTH(date_paiement) as mois'),
            DB::raw('YEAR(date_paiement) as annee'),
            DB::raw('SUM(montant_paye) as total')
        )
            ->where('statut', 'paye')
            ->whereBetween('date_paiement', [now()->subMonths(5), now()])
            ->groupBy('annee', 'mois')
            ->orderBy('annee')
            ->orderBy('mois')
            ->get();

        // ── TOP ÉQUIPES ───────────────────────────────
        $top_equipes = Equipe::withCount([
            'reservationsA as total_matchs_a',
            'reservationsB as total_matchs_b',
        ])
            ->get()
            ->map(function ($e) {
                $e->total_matchs = $e->total_matchs_a + $e->total_matchs_b;

                return $e;
            })
            ->sortByDesc('total_matchs')
            ->take(5);

        // ── ACTIVITÉ CRÉNEAUX ─────────────────────────
        $activite_creneaux = Reservation::select(
            'creneau',
            DB::raw('COUNT(*) as total')
        )
            ->where('statut', '!=', 'annule')
            ->groupBy('creneau')
            ->orderByDesc('total')
            ->get();

        return view('dashboard', compact(
            'stats',
            'prochaines_reservations',
            'derniers_paiements',
            'reservations_semaine',
            'revenus_mois',
            'top_equipes',
            'activite_creneaux'
        ));
    }
}
