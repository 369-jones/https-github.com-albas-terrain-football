@extends('layouts.main')
@section('title', 'Nouvelle réservation')
@section('breadcrumb', 'Nouvelle réservation')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">➕ Nouvelle réservation</div>
        <div class="page-subtitle">Programmer une rencontre sur le terrain</div>
    </div>
    <a href="{{ route('reservations.index') }}" class="btn btn-outline">← Retour</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">📋 Informations de la rencontre</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('reservations.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>Équipe A (Locale) *</label>
                    <select name="equipe_a_id">
                        <option value="">Sélectionner l'équipe A...</option>
                        @foreach($equipes as $equipe)
                        <option value="{{ $equipe->id }}"
                            {{ old('equipe_a_id') == $equipe->id ? 'selected' : '' }}>
                            {{ $equipe->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('equipe_a_id')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Équipe B (Visiteur) *</label>
                    <select name="equipe_b_id">
                        <option value="">Sélectionner l'équipe B...</option>
                        @foreach($equipes as $equipe)
                        <option value="{{ $equipe->id }}"
                            {{ old('equipe_b_id') == $equipe->id ? 'selected' : '' }}>
                            {{ $equipe->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('equipe_b_id')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Date de la rencontre *</label>
                    <input type="date" name="date_match"
                           value="{{ old('date_match') }}"
                           min="{{ date('Y-m-d') }}">
                    @error('date_match')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Créneau horaire *</label>
                    <select name="creneau">
                        <option value="">Sélectionner un créneau...</option>
                        @foreach(['08h00-10h00','10h00-12h00','14h00-16h00','16h00-18h00','18h00-20h00'] as $c)
                        <option value="{{ $c }}" {{ old('creneau') == $c ? 'selected' : '' }}>
                            {{ $c }}
                        </option>
                        @endforeach
                    </select>
                    @error('creneau')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Type de compétition *</label>
                    <select name="type_match">
                        @foreach(['Match amical','Championnat universitaire','Coupe interfacultés','Tournoi'] as $t)
                        <option value="{{ $t }}" {{ old('type_match') == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Montant *</label>
                    <div style="display:flex;gap:8px">
                        <input type="number" name="montant"
                                value="{{ old('montant', 15000) }}"
                                min="0" step="500"
                                style="flex:1">
                        <select name="devise" id="deviseSelect" onchange="updateSymbole()"
                                style="width:160px">
                            @foreach(config('devises.liste') as $code => $info)
                            <option value="{{ $code }}"
                                {{ old('devise', 'XAF') == $code ? 'selected' : '' }}>
                                {{ $info['drapeau'] }} {{ $code }} — {{ $info['symbole'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @error('montant')<div class="error">{{ $message }}</div>@enderror
                    @error('devise')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="field">
                <label>Notes / Observations</label>
                <textarea name="notes" placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">💾 Enregistrer la réservation</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function updateSymbole() {
        const select  = document.getElementById('deviseSelect');
        const option  = select.options[select.selectedIndex];
        console.log('Devise sélectionnée :', option.value);
    }
</script>
@endsection
