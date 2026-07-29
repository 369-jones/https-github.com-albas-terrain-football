@extends('layouts.main')
@section('title', 'Paramètres')
@section('breadcrumb', 'Paramètres')

@section('content')

<div class="page-title"><i class="fa-solid fa-gear"></i> Paramètres</div>
<div class="page-subtitle">Configuration du compte administrateur</div>

<div class="two-cols">
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-user"></i> Informations du compte</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('parametres.update') }}">
                @csrf
                <div class="field">
                    <label>Nom complet *</label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Votre nom">
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Adresse e-mail *</label>
                    <input type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="admin@terrainfoot.com">
                    @error('email')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="password"
                           placeholder="Laisser vide pour conserver">
                    @error('password')<div class="error">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation"
                           placeholder="Confirmer le mot de passe">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Mettre à jour</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><i class="fa-solid fa-circle-info"></i> Informations du système</div>
        </div>
        <div class="panel-body">
            <table style="width:100%;font-size:14px">
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700">Application</td>
                    <td style="padding:12px 0;font-weight:700">{{ config('app.name') }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700">Version Laravel</td>
                    <td style="padding:12px 0">{{ app()->version() }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700">Environnement</td>
                    <td style="padding:12px 0">
                        <span class="badge badge-green">{{ app()->environment() }}</span>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:12px 0;color:#64748b;font-weight:700">Base de données</td>
                    <td style="padding:12px 0">MySQL — terrain_football</td>
                </tr>
                <tr>
                    <td style="padding:12px 0;color:#64748b;font-weight:700">Administrateur</td>
                    <td style="padding:12px 0">{{ $user->email }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection
