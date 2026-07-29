@extends('layouts.main')
@section('title', 'Détail paiement')
@section('breadcrumb', 'Détail paiement')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-sack-dollar"></i> Détail du paiement</div>
        <div class="page-subtitle">
            {{ $paiement->reservation->equipeA->nom }}
            vs {{ $paiement->reservation->equipeB->nom }}
        </div>
    </div>
    <div style="display:flex;gap:10px">
        @if($paiement->statut === 'paye' && $paiement->facture)
        <a href="{{ route('factures.pdf', $paiement->facture) }}" class="btn btn-primary">
            <i class="fa-solid fa-file-invoice"></i> Voir la facture
        </a>
        @endif
        <a href="{{ route('paiements.index') }}" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-sack-dollar"></i> Informations du paiement</div>
        </div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:45%">Montant dû</td>
                    <td style="padding:10px 0;font-weight:700">
                        {{ number_format($paiement->montant_du, 0, ',', ' ') }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Montant payé</td>
                    <td style="padding:10px 0;font-weight:800;font-size:18px;color:#15803d">
                        {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                    </td>
                </tr>
                @if($paiement->resteAPayer() > 0)
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Reste à payer</td>
                    <td style="padding:10px 0;font-weight:700;color:#dc2626">
                        {{ number_format($paiement->resteAPayer(), 0, ',', ' ') }}
                    </td>
                </tr>
                @endif
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Mode</td>
                    <td style="padding:10px 0">
                        <span class="badge badge-gray">{{ $paiement->mode_paiement }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Date</td>
                    <td style="padding:10px 0">
                        {{ $paiement->date_paiement->format('d/m/Y') }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Référence</td>
                    <td style="padding:10px 0">{{ $paiement->reference ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Statut</td>
                    <td style="padding:10px 0">{!! $paiement->statutBadge() !!}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-calendar-check"></i> Réservation liée</div>
        </div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:45%">Date match</td>
                    <td style="padding:10px 0;font-weight:700">
                        {{ $paiement->reservation->date_match->format('d/m/Y') }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Créneau</td>
                    <td style="padding:10px 0">
                        <span style="font-weight:800;font-size:18px;color:#1d4ed8">
                            @montant($reservation->montant, $reservation->devise)
                        </span>
                        <span style="font-size:12px;color:#64748b;margin-left:8px">
                            {{ config('devises.liste')[$reservation->devise]['drapeau'] ?? '' }}
                            {{ config('devises.liste')[$reservation->devise]['nom'] ?? $reservation->devise }}
                        </span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:45%">Montant dû</td>
                    <td style="padding:10px 0;font-weight:700">
                        @montant($paiement->montant_du, $paiement->devise)
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Montant payé</td>
                    <td style="padding:10px 0;font-weight:800;font-size:18px;color:#15803d">
                        @montant($paiement->montant_paye, $paiement->devise)
                    </td>
                </tr>
                @if($paiement->resteAPayer() > 0)
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Reste à payer</td>
                    <td style="padding:10px 0;font-weight:700;color:#dc2626">
                        @montant($paiement->resteAPayer(), $paiement->devise)
                    </td>
                </tr>
                @endif
            </table>
            <div style="margin-top:16px">
                <a href="{{ route('reservations.show', $paiement->reservation) }}"
                   class="btn btn-outline" style="width:100%;justify-content:center">
                    Voir la réservation →
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
