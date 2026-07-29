@extends('layouts.main')
@section('title', 'Paiements')
@section('breadcrumb', 'Paiements')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">💰 Paiements</div>
        <div class="page-subtitle">Historique de tous les paiements enregistrés</div>
    </div>
    <a href="{{ route('paiements.create') }}" class="btn btn-success">+ Enregistrer un paiement</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Liste des paiements ({{ $paiements->count() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Réservation</th>
                    <th>Équipes</th>
                    <th>Montant dû</th>
                    <th>Montant payé</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paiements as $p)
                <tr>
                    <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>
                    <td>
                        <span class="badge badge-blue">
                            #{{ $p->reservation_id }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:700">{{ $p->reservation->equipeA->nom }}</div>
                        <div style="font-size:12px;color:#64748b">vs {{ $p->reservation->equipeB->nom }}</div>
                    </td>
                    <td style="font-weight:700">
                        @montant($p->montant, $p->devise)
                        <div style="font-size:11px;color:#94a3b8">
                            {{ config('devises.liste')[$p->devise]['drapeau'] ?? '' }}
                            {{ $p->devise }}
                        </div>
                    </td>
                    <td style="font-weight:800;color:#15803d">
                         @montant($p->montant_paye, $p->devise)
                    </td>
                    <td>
                        <span class="badge badge-gray">{{ $p->mode_paiement }}</span>
                    </td>
                    <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
                    <td>{!! $p->statutBadge() !!}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('paiements.show', $p) }}"
                               class="btn btn-outline btn-sm">👁 Voir</a>
                            @if($p->statut === 'paye' && $p->facture)
                            <a href="{{ route('factures.pdf', $p->facture) }}"
                               class="btn btn-primary btn-sm">🧾 Reçu</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="table-empty">
                        Aucun paiement enregistré —
                        <a href="{{ route('paiements.create') }}" style="color:#2563eb">
                            Enregistrer un paiement
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
