@extends('layouts.main')
@section('title', 'Détail équipe')
@section('breadcrumb', 'Détail équipe')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">👥 {{ $equipe->nom }}</div>
        <div class="page-subtitle">Détails et historique des rencontres</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('equipes.edit', $equipe) }}" class="btn btn-primary">✏ Modifier</a>
        <a href="{{ route('equipes.index') }}" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">📋 Informations</div>
        </div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:40%">Nom</td>
                    <td style="padding:10px 0;font-weight:700">{{ $equipe->nom }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Responsable</td>
                    <td style="padding:10px 0">{{ $equipe->responsable }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Contact</td>
                    <td style="padding:10px 0">{{ $equipe->contact }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Email</td>
                    <td style="padding:10px 0">{{ $equipe->email ?? '—' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Faculté</td>
                    <td style="padding:10px 0">{{ $equipe->faculte ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Statut</td>
                    <td style="padding:10px 0">
                        @if($equipe->statut === 'actif')
                            <span class="badge badge-green">Actif</span>
                        @else
                            <span class="badge badge-red">Inactif</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">📊 Statistiques</div>
        </div>
        <div class="panel-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div style="text-align:center;padding:20px;background:#eff6ff;border-radius:10px">
                    <div style="font-size:32px;font-weight:800;color:#1d4ed8">
                        {{ $equipe->reservationsA->count() + $equipe->reservationsB->count() }}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px">Total matchs</div>
                </div>
                <div style="text-align:center;padding:20px;background:#f0fdf4;border-radius:10px">
                    <div style="font-size:32px;font-weight:800;color:#15803d">
                        {{ $equipe->reservationsA->where('statut','confirme')->count() + $equipe->reservationsB->where('statut','confirme')->count() }}
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:4px">Confirmés</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- HISTORIQUE RESERVATIONS --}}
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">🗓️ Historique des rencontres</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Adversaire</th>
                    <th>Type</th>
                    <th>Créneau</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipe->reservations as $r)
                <tr>
                    <td style="font-weight:700">{{ $r->date_match->format('d/m/Y') }}</td>
                    <td>
                        @if($r->equipe_a_id === $equipe->id)
                            vs {{ $r->equipeB->nom }}
                        @else
                            vs {{ $r->equipeA->nom }}
                        @endif
                    </td>
                    <td><span class="badge badge-blue">{{ $r->type_match }}</span></td>
                    <td>{{ $r->creneau }}</td>
                    <td style="font-weight:700">{{ number_format($r->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{!! $r->statutBadge() !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-empty">Aucune rencontre enregistrée</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
