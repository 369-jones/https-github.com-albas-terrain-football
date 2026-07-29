@extends('layouts.main')
@section('title', 'Modifier réservation')
@section('breadcrumb', 'Modifier réservation')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">✏ Modifier la réservation</div>
        <div class="page-subtitle">{{ $reservation->equipeA->nom }} vs {{ $reservation->equipeB->nom }}</div>
    </div>
    <a href="{{ route('reservations.index') }}" class="btn btn-outline">← Retour</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">📋 Informations de la rencontre</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('reservations.update', $reservation) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label>Équipe A (Locale) *</label>
                    <select name="equipe_a_id">
                        @foreach($equipes as $equipe)
                        <option value="{{ $equipe->id }}"
                            {{ old('equipe_a_id', $reservation->equipe_a_id) == $equipe->id ? 'selected' : '' }}>
                            {{ $equipe->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('equipe_a_id')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Équipe B (Visiteur) *</label>
                    <select name="equipe_b_id">
                        @foreach($equipes as $equipe)
                        <option value="{{ $equipe->id }}"
                            {{ old('equipe_b_id', $reservation->equipe_b_id) == $equipe->id ? 'selected' : '' }}>
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
                           value="{{ old('date_match', $reservation->date_match->format('Y-m-d')) }}">
                    @error('date_match')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Créneau horaire *</label>
                    <select name="creneau">
                        @foreach(['08h00-10h00','10h00-12h00','14h00-16h00','16h00-18h00','18h00-20h00'] as $c)
                        <option value="{{ $c }}"
                            {{ old('creneau', $reservation->creneau) == $c ? 'selected' : '' }}>
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
                        <option value="{{ $t }}"
                            {{ old('type_match', $reservation->type_match) == $t ? 'selected' : '' }}>
                            {{ $t }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Montant (FCFA) *</label>
                    <input type="number" name="montant"
                           value="{{ old('montant', $reservation->montant) }}"
                           min="0" step="500">
                    @error('montant')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="field">
                <label>Statut</label>
                <select name="statut">
                    <option value="en_attente" {{ $reservation->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="confirme"   {{ $reservation->statut == 'confirme'   ? 'selected' : '' }}>Confirmé</option>
                    <option value="annule"     {{ $reservation->statut == 'annule'     ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="field">
                <label>Notes / Observations</label>
                <textarea name="notes" placeholder="Informations complémentaires...">
                    {{ old('notes', $reservation->notes) }}
                </textarea>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary">💾 Mettre à jour</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
