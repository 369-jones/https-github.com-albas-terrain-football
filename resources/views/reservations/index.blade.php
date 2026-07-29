@extends('layouts.main')
@section('title', 'Réservations')
@section('breadcrumb', 'Réservations')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">🗓️ Réservations</div>
        <div class="page-subtitle">Gérez toutes les rencontres programmées</div>
    </div>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary">+ Nouvelle réservation</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Liste des réservations ({{ $reservations->count() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Créneau</th>
                    <th>Équipe A</th>
                    <th>Équipe B</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                <tr>
                    <td style="color:#94a3b8;font-size:12px">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700">{{ $r->date_match->format('d/m/Y') }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $r->creneau }}</div>
                    </td>
                    <td style="font-weight:700">{{ $r->equipeA->nom }}</td>
                    <td style="font-weight:700">{{ $r->equipeB->nom }}</td>
                    <td><span class="badge badge-purple">{{ $r->type_match }}</span></td>
                    <td style="font-weight:700">
                        @montant($r->montant, $r->devise)
                    </td>
                    <td>
                        @if($r->paiement)
                            {!! $r->paiement->statutBadge() !!}
                        @else
                            <span class="badge badge-gray">Non payé</span>
                        @endif
                    </td>
                    <td>{!! $r->statutBadge() !!}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('reservations.show', $r) }}"
                               class="btn btn-outline btn-sm">👁</a>
                            <a href="{{ route('reservations.edit', $r) }}"
                               class="btn btn-primary btn-sm">✏</a>
                            @if(!$r->paiement || $r->paiement->statut !== 'paye')
                            <a href="{{ route('paiements.create') }}?reservation_id={{ $r->id }}"
                               class="btn btn-success btn-sm">💰</a>
                            @endif
                            @if($r->statut !== 'annule')
                            <form method="POST" action="{{ route('reservations.destroy', $r) }}"
                                  onsubmit="return confirm('Annuler cette réservation ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">✕</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="table-empty">
                        Aucune réservation —
                        <a href="{{ route('reservations.create') }}" style="color:#2563eb">
                            Créer une réservation
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
