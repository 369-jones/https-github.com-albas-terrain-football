<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #0f172a;
            background: #fff;
        }

        /* ── EN-TÊTE ── */
        .header {
            background: #1d4ed8;
            color: white;
            padding: 24px 32px;
            margin-bottom: 24px;
        }
        .header-inner {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }
        .logo-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }
        .logo-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            margin-bottom: 2px;
        }
        .facture-label {
            font-size: 11px;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 4px;
        }
        .facture-number {
            font-size: 22px;
            font-weight: 700;
            color: white;
        }
        .facture-date {
            font-size: 11px;
            color: rgba(255,255,255,0.8);
            margin-top: 4px;
        }

        /* ── PARTIES ── */
        .parties {
            display: table;
            width: 100%;
            padding: 0 32px;
            margin-bottom: 24px;
        }
        .partie {
            display: table-cell;
            width: 48%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            vertical-align: top;
        }
        .partie-spacer {
            display: table-cell;
            width: 4%;
        }
        .partie-label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .partie-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .partie-info {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 2px;
        }

        /* ── TABLE PRESTATIONS ── */
        .section {
            padding: 0 32px;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }
        .table-presta {
            width: 100%;
            border-collapse: collapse;
        }
        .table-presta thead tr {
            background: #1d4ed8;
            color: white;
        }
        .table-presta thead th {
            padding: 9px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
        }
        .table-presta thead th.right { text-align: right; }
        .table-presta tbody tr {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .table-presta tbody td {
            padding: 12px;
            vertical-align: top;
        }
        .table-presta tbody td.right { text-align: right; }
        .presta-name { font-weight: 700; font-size: 13px; margin-bottom: 3px; }
        .presta-sub  { font-size: 11px; color: #64748b; }

        /* ── TOTAUX ── */
        .totaux {
            padding: 0 32px;
            margin-bottom: 24px;
        }
        .totaux-inner {
            float: right;
            width: 280px;
        }
        .totaux-line {
            display: table;
            width: 100%;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .totaux-line-label {
            display: table-cell;
            font-size: 12px;
            color: #64748b;
        }
        .totaux-line-value {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .totaux-grand {
            display: table;
            width: 100%;
            padding: 10px 0;
            border-top: 2px solid #1d4ed8;
            margin-top: 4px;
        }
        .totaux-grand-label {
            display: table-cell;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .totaux-grand-value {
            display: table-cell;
            text-align: right;
            font-size: 18px;
            font-weight: 700;
            color: #15803d;
        }
        .clearfix { clear: both; }

        /* ── TAMPON ── */
        .tampon-wrap {
            padding: 0 32px;
            margin-bottom: 24px;
            text-align: center;
        }
        .tampon {
            display: inline-block;
            border: 3px solid #16a34a;
            border-radius: 8px;
            padding: 8px 28px;
            color: #16a34a;
            font-size: 18px;
            font-weight: 700;
        }
        .tampon-partiel {
            border-color: #ea580c;
            color: #ea580c;
        }
        .reste {
            display: inline-block;
            margin-left: 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 8px 16px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 700;
        }

        /* ── PIED ── */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding: 16px 32px 0;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    {{-- EN-TÊTE --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <div class="logo-title">{{ config('app.name') }}</div>
                <div class="logo-sub">Université Marcelo</div>
                <div class="logo-sub">Kinshasa · RDCongo</div>
            </div>
            <div class="header-right">
                <div class="facture-label">Facture officielle</div>
                <div class="facture-number">{{ $facture->numero_facture }}</div>
                <div class="facture-date">
                    Émise le : {{ $facture->date_emission->format('d/m/Y') }}
                </div>
                @if($facture->paiement->reference)
                <div class="facture-date">
                    Réf : {{ $facture->paiement->reference }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- PARTIES --}}
    <div class="parties">
        <div class="partie">
            <div class="partie-label">Émis par</div>
            <div class="partie-name">{{ config('app.name') }}</div>
            <div class="partie-info">Université Marcelo</div>
            <div class="partie-info">Kinshasa · RDCongo</div>
            <div class="partie-info">admin@terrainfoot.com</div>
        </div>
        <div class="partie-spacer"></div>
        <div class="partie">
            <div class="partie-label">Facturé à</div>
            <div class="partie-name">{{ $facture->reservation->equipeA->nom }}</div>
            <div class="partie-info">
                Responsable : {{ $facture->reservation->equipeA->responsable }}
            </div>
            <div class="partie-info">
                Contact : {{ $facture->reservation->equipeA->contact }}
            </div>
            <div class="partie-info">
                Réservation : #{{ $facture->reservation_id }}
            </div>
        </div>
    </div>

    {{-- DÉTAIL PRESTATION --}}
    <div class="section">
        <div class="section-title">Détail de la prestation</div>
        <table class="table-presta">
            <thead>
                <tr>
                    <th style="width:40%">Description</th>
                    <th style="width:15%">Date</th>
                    <th style="width:20%">Créneau</th>
                    <th style="width:25%" class="right">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="presta-name">
                            {{ $facture->reservation->type_match }}
                        </div>
                        <div class="presta-sub">
                            {{ $facture->reservation->equipeA->nom }}
                            vs {{ $facture->reservation->equipeB->nom }}
                        </div>
                    </td>
                    <td>
                        {{ $facture->reservation->date_match->format('d/m/Y') }}
                    </td>
                    <td>{{ $facture->reservation->creneau }}</td>
                    <td class="right" style="font-weight:700">
                        @montant($facture->montant, $facture->devise)
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- TOTAUX --}}
    <div class="totaux">
        <div class="totaux-inner">
            <div class="totaux-line">
                <div class="totaux-line-label">Montant dû</div>
                <div class="totaux-line-value">
                    @montant($facture->paiement->montant_du, $facture->devise)
                </div>
            </div>
            <div class="totaux-line">
                <div class="totaux-line-label">Mode de paiement</div>
                <div class="totaux-line-value">{{ $facture->paiement->mode_paiement }}</div>
            </div>
            <div class="totaux-grand">
                <div class="totaux-grand-label">TOTAL PAYÉ</div>
                <div class="totaux-grand-value">
                    @montant($facture->montant, $facture->devise)
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    {{-- TAMPON --}}
    <div class="tampon-wrap">
        @if($facture->paiement->resteAPayer() > 0)
            <span class="tampon tampon-partiel">⚠ PAIEMENT PARTIEL</span>
            <span class="reste">
                Reste : @montant($facture->paiement->resteAPayer(), $facture->devise)
            </span>
        @else
            <span class="tampon">✓ PAYÉE</span>
        @endif
    </div>

    {{-- PIED --}}
    <div class="footer">
        <div>Merci de votre confiance — {{ config('app.name') }}</div>
        <div>Université Marcelo · Kinshasa · RDCongo</div>
        <div style="margin-top:4px;font-style:italic">
            Ce document est une facture officielle valable comme preuve de paiement.
            Généré le {{ now()->format('d/m/Y à H:i') }}.
        </div>
    </div>

</body>
</html>
