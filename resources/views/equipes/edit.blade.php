@extends('layouts.main')
@section('title', 'Modifier équipe')
@section('breadcrumb', 'Modifier équipe')

@section('content')

<div class="flex-between">
    <div>
        <div class="page-title"><i class="fa-solid fa-pen"></i> Modifier l'équipe</div>
        <div class="page-subtitle">{{ $equipe->nom }}</div>
    </div>
    <a href="{{ route('equipes.index') }}" class="btn btn-outline">← Retour</a>
</div>

<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><i class="fa-solid fa-clipboard-list"></i> Informations de l'équipe</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('equipes.update', $equipe) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label>Nom de l'équipe *</label>
                    <input type="text" name="nom"
                           value="{{ old('nom', $equipe->nom) }}"
                           placeholder="Ex: AS Polytechnique">
                    @error('nom')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Responsable *</label>
                    <input type="text" name="responsable"
                           value="{{ old('responsable', $equipe->responsable) }}"
                           placeholder="Nom du responsable">
                    @error('responsable')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Contact *</label>
                    <input type="text" name="contact"
                           value="{{ old('contact', $equipe->contact) }}"
                           placeholder="+225 07 XX XX XX XX">
                    @error('contact')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', $equipe->email) }}"
                           placeholder="equipe@terrainfoot.com">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="field">
                    <label>Faculté / UFR</label>
                    <input type="text" name="faculte"
                           value="{{ old('faculte', $equipe->faculte) }}"
                           placeholder="Ex: UFR Sciences">
                </div>
                <div class="field">
                    <label>Statut</label>
                    <select name="statut">
                        <option value="actif"   {{ $equipe->statut === 'actif'   ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ $equipe->statut === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Mettre à jour</button>
                <a href="{{ route('equipes.index') }}" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</div>

@endsection
