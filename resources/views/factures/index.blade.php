@extends('layouts.main')
@section('title', 'Factures')
@section('breadcrumb', 'Factures & Reçus')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-file-invoice"></i> Factures & Reçus</div>
        <div class="page-subtitle">Consultez et téléchargez les factures générées</div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Liste des factures ({{ $factures->count() }})</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>N° Facture</th>
                    <th>Réservation</th>
                    <th>Équipes</th>
                    <th>Montant</th>
                    <th>Date émission</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factures as $f)
                <tr>
                    <td style="font-weight:800;color:#2563eb">{{ $f->numero_facture }}</td>
                    <td><span class="badge badge-blue">#{{ $f->reservation_id }}</span></td>
                    <td>
                        <div style="font-weight:700">{{ $f->reservation->equipeA->nom }}</div>
                        <div style="font-size:12px;color:#64748b">
                            vs {{ $f->reservation->equipeB->nom }}
                        </div>
                    </td>
                    <td style="font-weight:800;color:#15803d">
                        {{ number_format($f->montant, 0, ',', ' ') }}
                    </td>
                    <td>{{ $f->date_emission->format('d/m/Y') }}</td>
                    <td>{!! $f->statutBadge() !!}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('factures.show', $f) }}"
                               class="btn btn-outline btn-sm"><i class="fa-solid fa-eye"></i> Voir</a>
                            <a href="{{ route('factures.pdf', $f) }}"
                               class="btn btn-primary btn-sm"><i class="fa-solid fa-file-invoice"></i> PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        Aucune facture générée — Les factures sont créées automatiquement après paiement
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
