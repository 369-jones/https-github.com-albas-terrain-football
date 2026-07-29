@extends('layouts.main')
@section('title', 'Reçu de paiement')
@section('breadcrumb', 'Reçu')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">🧾 Reçu de paiement</div>
    </div>
    <div style="display:flex;gap:10px">
        <button onclick="window.print()" class="btn btn-primary">🖨 Imprimer</button>
        <a href="{{ route('paiements.index') }}" class="btn btn-outline">← Retour</a>
    </div>
</div>

<div class="panel">
    <div class="panel-body">
        <div style="max-width:700px;margin:0 auto;font-family:'Segoe UI',sans-serif">

            {{-- EN-TÊTE --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-start;
                        border-bottom:3px solid #2563eb;padding-bottom:20px;margin-bottom:24px">
                <div>
                    <div style="font-size:32px">⚽</div>
                    <div style="font-size:22px;font-weight:800;color:#2563eb">
                        Terrain Football Universitaire
                    </div>
                    <div style="color:#64748b;font-size:13px">Université Marcelo</div>
                    <div style="color:#64748b;font-size:13px">Kinshasa · RDCongo</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:12px;color:#64748b">REÇU DE PAIEMENT</div>
                    @if($paiement->facture)
                    <div style="font-size:22px;font-weight:800">
                        {{ $paiement->facture->numero_facture }}
                    </div>
                    @endif
                    <div style="color:#64748b;font-size:13px">
                        Date : {{ $paiement->date_paiement->format('d/m/Y') }}
                    </div>
                    @if($paiement->reference)
                    <div style="color:#64748b;font-size:13px">Réf : {{ $paiement->reference }}</div>
                    @endif
                </div>
            </div>

            {{-- PARTIES --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px">
                <div style="background:#f8fafc;padding:16px;border-radius:10px">
                    <div style="font-size:10px;font-weight:800;color:#64748b;
                                text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">
                        Émis par
                    </div>
                    <div style="font-weight:800;font-size:15px">Terrain Football Universitaire</div>
                    <div style="color:#64748b;font-size:13px">Université Marcelo</div>
                    <div style="color:#64748b;font-size:13px">Kinshasa · RDCongo</div>
                </div>
                <div style="background:#f8fafc;padding:16px;border-radius:10px">
                    <div style="font-size:10px;font-weight:800;color:#64748b;
                                text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">
                        Destinataire
                    </div>
                    <div style="font-weight:800;font-size:15px">
                        {{ $paiement->reservation->equipeA->nom }}
                    </div>
                    <div style="color:#64748b;font-size:13px">
                        vs {{ $paiement->reservation->equipeB->nom }}
                    </div>
                    <div style="color:#64748b;font-size:13px">
                        Réservation #{{ $paiement->reservation_id }}
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
                <thead>
                    <tr style="background:#2563eb;color:white">
                        <th style="padding:10px 14px;text-align:left;font-size:11px;
                                   text-transform:uppercase;letter-spacing:0.05em">Description</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;
                                   text-transform:uppercase;letter-spacing:0.05em">Date</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;
                                   text-transform:uppercase;letter-spacing:0.05em">Créneau</th>
                        <th style="padding:10px 14px;text-align:right;font-size:11px;
                                   text-transform:uppercase;letter-spacing:0.05em">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background:#f8fafc;border:1px solid #e2e8f0">
                        <td style="padding:14px">
                            <div style="font-weight:700">
                                {{ $paiement->reservation->type_match }}
                            </div>
                            <div style="font-size:12px;color:#64748b">
                                {{ $paiement->reservation->equipeA->nom }}
                                vs {{ $paiement->reservation->equipeB->nom }}
                            </div>
                        </td>
                        <td style="padding:14px">
                            {{ $paiement->reservation->date_match->format('d/m/Y') }}
                        </td>
                        <td style="padding:14px">{{ $paiement->reservation->creneau }}</td>
                        <td style="padding:14px;text-align:right;font-weight:700">
                            {{ number_format($paiement->montant_du, 0, ',', ' ') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- TOTAUX --}}
            <div style="text-align:right;margin-bottom:24px">
                <div style="display:flex;justify-content:flex-end;gap:40px;
                            margin-bottom:6px;font-size:13px;color:#64748b">
                    <span>Montant dû :</span>
                    <span style="font-weight:700;color:#0f172a">
                        {{ number_format($paiement->montant_du, 0, ',', ' ') }}
                    </span>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:40px;
                            margin-bottom:6px;font-size:13px;color:#64748b">
                    <span>Mode de paiement :</span>
                    <span style="font-weight:700;color:#0f172a">{{ $paiement->mode_paiement }}</span>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:40px;
                            border-top:2px solid #e2e8f0;padding-top:10px;margin-top:6px">
                    <span style="font-size:16px;font-weight:800">MONTANT PAYÉ :</span>
                    <span style="font-size:20px;font-weight:800;color:#15803d">
                        {{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                    </span>
                </div>
                @if($paiement->resteAPayer() > 0)
                <div style="display:flex;justify-content:flex-end;gap:40px;
                            margin-top:6px;color:#dc2626">
                    <span style="font-weight:700">Reste à payer :</span>
                    <span style="font-weight:800">
                        {{ number_format($paiement->resteAPayer(), 0, ',', ' ') }}
                    </span>
                </div>
                @endif
            </div>

            {{-- TAMPON --}}
            <div style="text-align:center;margin:24px 0">
                @if($paiement->statut === 'paye')
                <div style="display:inline-block;padding:8px 28px;
                            border:3px solid #16a34a;border-radius:8px;
                            color:#16a34a;font-weight:800;font-size:18px;
                            transform:rotate(-5deg)">
                    ✓ PAYÉ
                </div>
                @else
                <div style="display:inline-block;padding:8px 28px;
                            border:3px solid #ea580c;border-radius:8px;
                            color:#ea580c;font-weight:800;font-size:16px;
                            transform:rotate(-5deg)">
                    ⚠ PAIEMENT PARTIEL
                </div>
                @endif
            </div>

            {{-- PIED --}}
            <div style="text-align:center;border-top:1px solid #e2e8f0;
                        padding-top:16px;color:#94a3b8;font-size:12px">
                <div>Merci de votre confiance — Terrain Football Universitaire</div>
                <div style="margin-top:4px">
                    Université Marcelo · Kinshasa · RDCongo
                </div>
                <div style="margin-top:4px;font-style:italic">
                    Ce document est un reçu officiel valable comme preuve de paiement.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
