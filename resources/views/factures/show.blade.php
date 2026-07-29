@extends('layouts.main')
@section('title', 'Détail facture')
@section('breadcrumb', 'Détail facture')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-file-invoice"></i> {{ $facture->numero_facture }}</div>
        <div class="page-subtitle">Détail de la facture</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('factures.pdf', $facture) }}" class="btn btn-primary"><i class="fa-solid fa-file-invoice"></i> Voir PDF</a>
        <a href="{{ route('factures.index') }}" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header"><div class="panel-title"><i class="fa-solid fa-clipboard-list"></i> Informations</div></div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:45%">N° Facture</td>
                    <td style="padding:10px 0;font-weight:800;color:#2563eb">
                        {{ $facture->numero_facture }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Montant</td>
                    <td style="padding:10px 0;font-weight:800;font-size:18px;color:#15803d">
                        {{ number_format($facture->montant, 0, ',', ' ') }} {{ $facture->devise }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Date émission</td>
                    <td style="padding:10px 0">{{ $facture->date_emission->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Statut</td>
                    <td style="padding:10px 0">{!! $facture->statutBadge() !!}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header"><div class="panel-title"><i class="fa-solid fa-calendar-check"></i> Réservation</div></div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700;width:45%">Équipe A</td>
                    <td style="padding:10px 0;font-weight:700">
                        {{ $facture->reservation->equipeA->nom }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Équipe B</td>
                    <td style="padding:10px 0;font-weight:700">
                        {{ $facture->reservation->equipeB->nom }}
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Date match</td>
                    <td style="padding:10px 0">
                        {{ $facture->reservation->date_match->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:10px 0;color:#64748b;font-weight:700">Mode paiement</td>
                    <td style="padding:10px 0">
                        <span class="badge badge-gray">{{ $facture->paiement->mode_paiement }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
