@extends('layouts.main')
@section('title', 'Nouvelle équipe')
@section('breadcrumb', 'Nouvelle équipe')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title">➕ Nouvelle équipe</div>
        <div class="page-subtitle">Inscrire une nouvelle équipe universitaire</div>
    </div>
    <a href="{{ route('equipes.index') }}" class="btn btn-outline">← Retour</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title">📋 Informations de l'équipe</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('equipes.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label>Nom de l'équipe *</label>
                    <input type="text" name="nom"
                           value="{{ old('nom') }}"
                           placeholder="Ex: AS Polytechnique">
                    @error('nom')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Responsable *</label>
                    <input type="text" name="responsable"
                           value="{{ old('responsable') }}"
                           placeholder="Nom du responsable">
                    @error('responsable')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Contact *</label>
                    <input type="text" name="contact"
                           value="{{ old('contact') }}"
                           placeholder="+225 07 XX XX XX XX">
                    @error('contact')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="equipe@universite.ci">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Faculté / UFR</label>
                    <input type="text" name="faculte"
                           value="{{ old('faculte') }}"
                           placeholder="Ex: UFR Sciences">
                    @error('faculte')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary">💾 Enregistrer l'équipe</button>
                <a href="{{ route('equipes.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
