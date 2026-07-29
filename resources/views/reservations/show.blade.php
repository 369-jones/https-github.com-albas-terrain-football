@extends('layouts.main')
@section('title', 'Détail réservation')
@section('breadcrumb', 'Détail réservation')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-clipboard-list"></i> Détail de la réservation</div>
        <div class="page-subtitle">
            {{ $reservation->equipeA->nom }} vs {{ $reservation->equipeB->nom }}
        </div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Modifier</a>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-calendar-check"></i> Informations</div>
        </div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:40%">Date</td>
                    <td style="padding:10px 0;font-weight:700">
                        {{ $reservation->date_match->format('d/m/Y') }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Créneau</td>
                    <td style="padding:10px 0">
                        <span class="badge badge-blue">{{ $reservation->creneau }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Équipe A</td>
                    <td style="padding:10px 0;font-weight:700">{{ $reservation->equipeA->nom }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Équipe B</td>
                    <td style="padding:10px 0;font-weight:700">{{ $reservation->equipeB->nom }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Type</td>
                    <td style="padding:10px 0">
                        <span class="badge badge-purple">{{ $reservation->type_match }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Montant</td>
                    <td style="padding:10px 0;font-weight:800;font-size:18px;color:#1d4ed8">
                        @montant($reservation->montant, $reservation->devise)
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Statut</td>
                    <td style="padding:10px 0">{!! $reservation->statutBadge() !!}</td>
                </tr>
            </table>
            @if($reservation->notes)
            <div style="margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px">
                <div style="font-size:11px;font-weight:700;color:#64748b;margin-bottom:6px">NOTES</div>
                <div style="font-size:13px">{{ $reservation->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-sack-dollar"></i> Paiement</div>
            @if(!$reservation->paiement || $reservation->paiement->statut !== 'paye')
            <a href="{{ route('paiements.create') }}?reservation_id={{ $reservation->id }}"
               class="btn btn-success btn-sm">+ Enregistrer paiement</a>
            @endif
        </div>
        <div class="panel-body">
            @if($reservation->paiement)
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:50%">Montant dû</td>
                    <td style="padding:10px 0;font-weight:700">
                        @montant($reservation->paiement->montant_du, $reservation->devise)
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Montant payé</td>
                    <td style="padding:10px 0;font-weight:800;color:#15803d">
                        @montant($reservation->paiement->montant_paye)
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Mode</td>
                    <td style="padding:10px 0">{{ $reservation->paiement->mode_paiement }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Date</td>
                    <td style="padding:10px 0">
                        {{ $reservation->paiement->date_paiement->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Statut</td>
                    <td style="padding:10px 0">{!! $reservation->paiement->statutBadge() !!}</td>
                </tr>
            </table>
            @if($reservation->paiement->statut === 'paye' && $reservation->facture)
            <div style="margin-top:16px">
                <a href="{{ route('factures.pdf', $reservation->facture) }}"
                   class="btn btn-primary" style="width:100%;justify-content:center">
                    <i class="fa-solid fa-file-invoice"></i> Voir la facture PDF
                </a>
            </div>
            @endif
            @else
            <div style="text-align:center;padding:30px;color:#94a3b8">
                <div style="font-size:32px;margin-bottom:8px"><i class="fa-solid fa-sack-dollar"></i></div>
                <div>Aucun paiement enregistré</div>
                <a href="{{ route('paiements.create') }}?reservation_id={{ $reservation->id }}"
                   class="btn btn-success" style="margin-top:12px">
                    Enregistrer un paiement
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
