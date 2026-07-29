@extends('layouts.main')
@section('title', 'Enregistrer un paiement')
@section('breadcrumb', 'Nouveau paiement')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-sack-dollar"></i> Enregistrer un paiement</div>
        <div class="page-subtitle">Associer un paiement à une réservation</div>
    </div>
    <a href="{{ route('paiements.index') }}" class="btn btn-outline">← Retour</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fa-solid fa-clipboard-list"></i> Informations du paiement</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('paiements.store') }}">
            @csrf
            <div class="field">
                <label>Réservation concernée *</label>
                <select name="reservation_id" id="reservationSelect" onchange="updateMontant()">
                    <option value="">Sélectionner une réservation...</option>
                    @foreach($reservations as $r)
                    <option value="{{ $r->id }}"
                        data-montant="{{ $r->montant }}"
                        data-devise="{{ $r->devise }}"
                        {{ (old('reservation_id') == $r->id || request('reservation_id') == $r->id) ? 'selected' : '' }}>
                        #{{ $r->id }} — {{ $r->equipeA->nom }} vs {{ $r->equipeB->nom }}
                        ({{ $r->date_match->format('d/m/Y') }}) —
                        @montant($r->montant, $r->devise)
                    </option>
                    @endforeach
                </select>
                @error('reservation_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Montant payé () *</label>
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="number" name="montant_paye" id="montantPaye"
                               value="{{ old('montant_paye') }}"
                               min="1" step="500"
                               placeholder="Ex: 15000"
                               style="flex:1">
                        <div id="deviseLabel" style="
                            padding: 10px 14px;
                            background: #f8fafc;
                            border: 1.5px solid #e2e8f0;
                            border-radius: 8px;
                            font-weight: 700;
                            color: #1d4ed8;
                            font-size: 13px;
                            min-width: 80px;
                            text-align: center;
                        ">—</div>
                    </div>
                    @error('montant_paye')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Mode de paiement *</label>
                    <select name="mode_paiement">
                        @foreach(['Espèces','Mobile Money','Virement','Chèque'] as $mode)
                        <option value="{{ $mode }}"
                            {{ old('mode_paiement') == $mode ? 'selected' : '' }}>
                            {{ $mode }}
                        </option>
                        @endforeach
                    </select>
                    @error('mode_paiement')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Date du paiement *</label>
                    <input type="date" name="date_paiement"
                           value="{{ old('date_paiement', date('Y-m-d')) }}">
                    @error('date_paiement')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Référence / N° Transaction</label>
                    <input type="text" name="reference"
                           value="{{ old('reference') }}"
                           placeholder="Ex: TXN-20240615-001">
                    @error('reference')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> Enregistrer le paiement</button>
                <a href="{{ route('paiements.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Données devises
    const devises = @json(config('devises.liste'));

    function updateMontant() {
        const select  = document.getElementById('reservationSelect');
        const option  = select.options[select.selectedIndex];
        const montant = option.dataset.montant;
        const devise  = option.dataset.devise;

        if (montant) {
            document.getElementById('montantPaye').value = montant;
        }
        if (devise && devises[devise]) {
            document.getElementById('deviseLabel').textContent =
                devises[devise].drapeau + ' ' + devise + ' — ' + devises[devise].symbole;
        } else {
            document.getElementById('deviseLabel').textContent = '—';
        }
    }

    window.onload = function() {
        const select = document.getElementById('reservationSelect');
        if (select.value) updateMontant();
    }
</script>
@endsection
