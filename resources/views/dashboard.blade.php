@extends('layouts.main')
@section('title', 'Dashboard')
@section('breadcrumb', 'Tableau de bord')

@section('content')

<div class="page-title"><i class="fa-solid fa-gauge"></i> Tableau de bord</div>
<div class="page-subtitle">
    Vue d'ensemble — {{ now()->translatedFormat('l d F Y') }}
</div>

{{-- ── STATS PRINCIPALES ── --}}
<div class="stats-grid">
    <div class="stat-card card-blue">
        <div>
            <div class="stat-label">Réservations ce mois</div>
            <div class="stat-value">{{ $stats['reservations_mois'] }}</div>
            <div class="stat-sub">Total : {{ $stats['total_reservations'] }}</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
    </div>
    <div class="stat-card card-green">
        <div>
            <div class="stat-label">Revenus ce mois</div>
            <div class="stat-value">
                {{ number_format($stats['revenus_mois'], 0, ',', ' ') }}
            </div>
            <div class="stat-sub">
                Total : @montant($stats['revenus_total'])
            </div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
    </div>
    <div class="stat-card card-orange">
        <div>
            <div class="stat-label">En attente</div>
            <div class="stat-value">{{ $stats['en_attente'] }}</div>
            <div class="stat-sub">À confirmer</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
    </div>
    <div class="stat-card card-purple">
        <div>
            <div class="stat-label">Équipes actives</div>
            <div class="stat-value">{{ $stats['total_equipes'] }}</div>
            <div class="stat-sub">Équipes inscrites</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-people-group"></i></div>
    </div>
    <div class="stat-card card-teal">
        <div>
            <div class="stat-label">Factures émises</div>
            <div class="stat-value">{{ $stats['total_factures'] }}</div>
            <div class="stat-sub">Reçus générés</div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-file-invoice"></i></div>
    </div>
    <div class="stat-card card-red">
        <div>
            <div class="stat-label">Impayés</div>
            <div class="stat-value">{{ $stats['impayes_count'] }}</div>
            <div class="stat-sub">
                @montant($stats['impayes_montant'])
            </div>
        </div>
        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    </div>
</div>

{{-- ── GRAPHIQUES LIGNE 1 ── --}}
<div class="two-cols" style="margin-bottom:20px">

    {{-- Activité 7 derniers jours --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-calendar-days"></i> Réservations — 7 derniers jours</div>
        </div>
        <div class="panel-body">
            @php
                $jours = [];
                $valeurs = [];
                $maxVal = 1;
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $label = now()->subDays($i)->format('d/m');
                    $total = $reservations_semaine[$date]->total ?? 0;
                    $jours[] = $label;
                    $valeurs[] = $total;
                    if ($total > $maxVal) $maxVal = $total;
                }
            @endphp
            <div style="display:flex;align-items:flex-end;gap:8px;height:120px;margin-bottom:8px">
                @foreach($valeurs as $i => $val)
                @php $hauteur = $maxVal > 0 ? max(4, round($val / $maxVal * 100)) : 4; @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                    <div style="font-size:11px;font-weight:700;color:#1d4ed8">
                        {{ $val > 0 ? $val : '' }}
                    </div>
                    <div style="
                        width:100%;
                        height:{{ $hauteur }}px;
                        background:{{ $val > 0 ? 'linear-gradient(180deg,#2563eb,#1d4ed8)' : '#f1f5f9' }};
                        border-radius:4px 4px 0 0;
                        transition:all 0.3s;
                    "></div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;gap:8px">
                @foreach($jours as $jour)
                <div style="flex:1;text-align:center;font-size:10px;color:#94a3b8;font-weight:700">
                    {{ $jour }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Revenus 6 derniers mois --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-sack-dollar"></i> Revenus — 6 derniers mois</div>
        </div>
        <div class="panel-body">
            @php
                $moisNoms = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
                $revenusMois = [];
                $maxRev = 1;
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $mois = (int)$date->format('m');
                    $annee = (int)$date->format('Y');
                    $found = $revenus_mois->first(fn($r) => $r->mois == $mois && $r->annee == $annee);
                    $total = $found ? $found->total : 0;
                    $revenusMois[] = ['label' => $moisNoms[$mois-1], 'total' => $total];
                    if ($total > $maxRev) $maxRev = $total;
                }
            @endphp
            <div style="display:flex;align-items:flex-end;gap:8px;height:120px;margin-bottom:8px">
                @foreach($revenusMois as $rev)
                @php $hauteur = $maxRev > 0 ? max(4, round($rev['total'] / $maxRev * 100)) : 4; @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                    <div style="font-size:10px;font-weight:700;color:#15803d">
                        {{ $rev['total'] > 0 ? number_format($rev['total']/1000, 0) . 'k' : '' }}
                    </div>
                    <div style="
                        width:100%;
                        height:{{ $hauteur }}px;
                        background:{{ $rev['total'] > 0 ? 'linear-gradient(180deg,#16a34a,#15803d)' : '#f1f5f9' }};
                        border-radius:4px 4px 0 0;
                    "></div>
                </div>
                @endforeach
            </div>
            <div style="display:flex;gap:8px">
                @foreach($revenusMois as $rev)
                <div style="flex:1;text-align:center;font-size:10px;color:#94a3b8;font-weight:700">
                    {{ $rev['label'] }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ── TABLEAUX LIGNE 2 ── --}}
<div class="two-cols" style="margin-bottom:20px">

    {{-- Prochaines rencontres --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-calendar-check"></i> Prochaines rencontres</div>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm">
                + Nouvelle
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Rencontre</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prochaines_reservations as $r)
                    <tr>
                        <td>
                            <div style="font-weight:700">
                                {{ $r->date_match->format('d/m/Y') }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8">{{ $r->creneau }}</div>
                        </td>
                        <td>
                            <div style="font-weight:700">{{ $r->equipeA->nom }}</div>
                            <div style="font-size:12px;color:#64748b">
                                vs {{ $r->equipeB->nom }}
                            </div>
                        </td>
                        <td style="font-weight:700;color:#1d4ed8">
                            @montant($r->montant, $r->devise)
                        </td>
                        <td>{!! $r->statutBadge() !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-empty">
                            Aucune réservation à venir
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($prochaines_reservations->count() > 0)
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;text-align:right">
            <a href="{{ route('reservations.index') }}"
               style="font-size:13px;color:#2563eb;font-weight:700;text-decoration:none">
                Voir toutes les réservations →
            </a>
        </div>
        @endif
    </div>

    {{-- Derniers paiements --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-sack-dollar"></i> Derniers paiements</div>
            <a href="{{ route('paiements.create') }}" class="btn btn-success btn-sm">
                + Paiement
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Équipes</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniers_paiements as $p)
                    <tr>
                        <td>
                            <div style="font-weight:700">
                                {{ $p->reservation->equipeA->nom }}
                            </div>
                            <div style="font-size:12px;color:#64748b">
                                vs {{ $p->reservation->equipeB->nom }}
                            </div>
                        </td>
                        <td style="font-weight:800;color:#15803d">
                            @montant($p->montant_paye, $p->devise)
                        </td>
                        <td>
                            <span class="badge badge-gray">{{ $p->mode_paiement }}</span>
                        </td>
                        <td>{!! $p->statutBadge() !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-empty">
                            Aucun paiement enregistré
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($derniers_paiements->count() > 0)
        <div style="padding:12px 16px;border-top:1px solid #f1f5f9;text-align:right">
            <a href="{{ route('paiements.index') }}"
               style="font-size:13px;color:#2563eb;font-weight:700;text-decoration:none">
                Voir tous les paiements →
            </a>
        </div>
        @endif
    </div>

</div>

{{-- ── LIGNE 3 ── --}}
<div class="two-cols">

    {{-- Top équipes --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-trophy"></i> Top équipes</div>
            <a href="{{ route('equipes.index') }}" class="btn btn-outline btn-sm">
                Voir tout
            </a>
        </div>
        <div class="panel-body">
            @forelse($top_equipes as $i => $equipe)
            @php
                $maxMatchs = $top_equipes->first()->total_matchs ?: 1;
                $pct = round($equipe->total_matchs / $maxMatchs * 100);
                $colors = ['#2563eb','#16a34a','#ea580c','#7c3aed','#0d9488'];
                $color = $colors[$i] ?? '#64748b';
            @endphp
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;
                            align-items:center;margin-bottom:5px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="
                            width:22px;height:22px;border-radius:50%;
                            background:{{ $color }};color:white;
                            font-size:11px;font-weight:800;
                            display:flex;align-items:center;justify-content:center;
                        ">{{ $i+1 }}</div>
                        <span style="font-weight:700;font-size:13px">{{ $equipe->nom }}</span>
                    </div>
                    <span class="badge badge-blue">{{ $equipe->total_matchs }} matchs</span>
                </div>
                <div style="background:#f1f5f9;border-radius:20px;height:8px">
                    <div style="
                        width:{{ $pct }}%;
                        background:{{ $color }};
                        height:100%;border-radius:20px;
                        transition:width 0.5s;
                    "></div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;padding:20px">
                Aucune équipe inscrite
            </div>
            @endforelse
        </div>
    </div>

    {{-- Activité créneaux --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">⏰ Créneaux les plus utilisés</div>
        </div>
        <div class="panel-body">
            @php $maxCreneau = $activite_creneaux->max('total') ?: 1; @endphp
            @forelse($activite_creneaux as $c)
            @php $pct = round($c->total / $maxCreneau * 100); @endphp
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;
                            margin-bottom:5px;font-size:13px">
                    <span style="font-weight:700">{{ $c->creneau }}</span>
                    <span style="color:#64748b">{{ $c->total }} réservation(s)</span>
                </div>
                <div style="background:#f1f5f9;border-radius:20px;height:8px">
                    <div style="
                        width:{{ $pct }}%;
                        background:linear-gradient(90deg,#7c3aed,#2563eb);
                        height:100%;border-radius:20px;
                    "></div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;padding:20px">
                Aucune donnée disponible
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── ALERTES ── --}}
@if($stats['en_attente'] > 0 || $stats['impayes_count'] > 0)
<div style="margin-top:4px">
    @if($stats['en_attente'] > 0)
    <div style="
        background:#fff7ed;border:1px solid #fdba74;border-radius:10px;
        padding:14px 18px;margin-bottom:10px;
        display:flex;align-items:center;justify-content:space-between;
    ">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:20px">⏳</span>
            <div>
                <div style="font-weight:700;color:#c2410c">
                    {{ $stats['en_attente'] }} réservation(s) en attente de confirmation
                </div>
                <div style="font-size:12px;color:#9a3412">
                    Ces réservations nécessitent un paiement pour être confirmées
                </div>
            </div>
        </div>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline btn-sm"
           style="border-color:#fdba74;color:#c2410c">
            Voir →
        </a>
    </div>
    @endif

    @if($stats['impayes_count'] > 0)
    <div style="
        background:#fef2f2;border:1px solid #fecaca;border-radius:10px;
        padding:14px 18px;
        display:flex;align-items:center;justify-content:space-between;
    ">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:20px"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
                <div style="font-weight:700;color:#b91c1c">
                    {{ $stats['impayes_count'] }} paiement(s) impayé(s)
                </div>
                <div style="font-size:12px;color:#991b1b">
                    Montant total :
                    @montant($stats['impayes_montant'])
                </div>
            </div>
        </div>
        <a href="{{ route('paiements.index') }}" class="btn btn-outline btn-sm"
           style="border-color:#fecaca;color:#b91c1c">
            Voir →
        </a>
    </div>
    @endif
</div>
@endif

@endsection