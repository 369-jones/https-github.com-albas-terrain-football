@extends('layouts.main')
@section('title', 'Rapports')
@section('breadcrumb', 'Rapports')

@section('content')

<div class="page-title">📈 Rapports & Statistiques</div>
<div class="page-subtitle">Analyse des activités du terrain universitaire</div>

<div class="stats-grid">
    <div class="stat-card card-blue">
        <div>
            <div class="stat-label">Total réservations</div>
            <div class="stat-value">{{ $stats['total_reservations'] }}</div>
            <div class="stat-sub">Depuis l'ouverture</div>
        </div>
        <div class="stat-icon">🗓️</div>
    </div>
    <div class="stat-card card-green">
        <div>
            <div class="stat-label">Revenus total</div>
            <div class="stat-value">{{ number_format($stats['revenus_total'], 0, ',', ' ') }}</div>
            <div class="stat-sub">FCFA encaissés</div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
    <div class="stat-card card-teal">
        <div>
            <div class="stat-label">Revenus ce mois</div>
            <div class="stat-value">{{ number_format($stats['revenus_mois'], 0, ',', ' ') }}</div>
            <div class="stat-sub">FCFA ce mois</div>
        </div>
        <div class="stat-icon">📅</div>
    </div>
    <div class="stat-card card-orange">
        <div>
            <div class="stat-label">En attente</div>
            <div class="stat-value">{{ $stats['en_attente'] }}</div>
            <div class="stat-sub">À confirmer</div>
        </div>
        <div class="stat-icon">⏳</div>
    </div>
    <div class="stat-card card-purple">
        <div>
            <div class="stat-label">Confirmés</div>
            <div class="stat-value">{{ $stats['confirmes'] }}</div>
            <div class="stat-sub">Réservations confirmées</div>
        </div>
        <div class="stat-icon">✅</div>
    </div>
    <div class="stat-card card-red">
        <div>
            <div class="stat-label">Impayés</div>
            <div class="stat-value">{{ number_format($stats['impayes_montant'], 0, ',', ' ') }}</div>
            <div class="stat-sub">FCFA non recouvrés</div>
        </div>
        <div class="stat-icon">⚠️</div>
    </div>
</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">💳 Paiements par mode</div>
        </div>
        <div class="panel-body">
            @forelse($paiements_par_mode as $mode)
            <div style="margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;
                            margin-bottom:6px;font-size:13px">
                    <span style="font-weight:700">{{ $mode->mode_paiement }}</span>
                    <span style="color:#64748b">
                        {{ $mode->total }} paiement(s) —
                        {{ number_format($mode->montant, 0, ',', ' ') }} FCFA
                    </span>
                </div>
                @php
                    $total = $paiements_par_mode->sum('montant');
                    $pct   = $total > 0 ? ($mode->montant / $total * 100) : 0;
                @endphp
                <div style="background:#f1f5f9;border-radius:20px;height:10px">
                    <div style="width:{{ $pct }}%;background:#2563eb;
                                height:100%;border-radius:20px;transition:width 0.5s">
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:#94a3b8;padding:20px">
                Aucun paiement enregistré
            </div>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">📊 Répartition des statuts</div>
        </div>
        <div class="panel-body">
            @php
                $total = $stats['total_reservations'] ?: 1;
                $pctConf = round($stats['confirmes'] / $total * 100);
                $pctAtt  = round($stats['en_attente'] / $total * 100);
                $pctAnn  = round($stats['annules'] / $total * 100);
            @endphp
            <div style="margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px">
                    <span style="font-weight:700;color:#15803d">✅ Confirmés</span>
                    <span>{{ $stats['confirmes'] }} ({{ $pctConf }}%)</span>
                </div>
                <div style="background:#f1f5f9;border-radius:20px;height:10px">
                    <div style="width:{{ $pctConf }}%;background:#16a34a;
                                height:100%;border-radius:20px"></div>
                </div>
            </div>
            <div style="margin-bottom:16px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px">
                    <span style="font-weight:700;color:#c2410c">⏳ En attente</span>
                    <span>{{ $stats['en_attente'] }} ({{ $pctAtt }}%)</span>
                </div>
                <div style="background:#f1f5f9;border-radius:20px;height:10px">
                    <div style="width:{{ $pctAtt }}%;background:#ea580c;
                                height:100%;border-radius:20px"></div>
                </div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13px">
                    <span style="font-weight:700;color:#b91c1c">❌ Annulés</span>
                    <span>{{ $stats['annules'] }} ({{ $pctAnn }}%)</span>
                </div>
                <div style="background:#f1f5f9;border-radius:20px;height:10px">
                    <div style="width:{{ $pctAnn }}%;background:#dc2626;
                                height:100%;border-radius:20px"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
