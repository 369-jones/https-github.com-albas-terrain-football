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

<div class="panel" style="margin-top:20px">
    <div class="panel-header">
        <div class="panel-title"><i class="fa-solid fa-key"></i> Clé API</div>
    </div>
    <div class="panel-body">
        @if (session('new_api_token'))
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:16px">
                <p style="font-weight:700;font-size:13px;margin-bottom:8px">
                    <i class="fa-solid fa-triangle-exclamation"></i> Copiez cette clé maintenant — elle ne sera plus jamais affichée.
                </p>
                <code style="display:block;background:#fff;border:1px solid #f1f5f9;border-radius:6px;padding:10px;font-size:13px;word-break:break-all">{{ session('new_api_token') }}</code>
            </div>
        @endif

        @if ($apiToken)
            <p style="color:#64748b;font-size:14px;margin-bottom:16px">
                Une clé API est active (générée le {{ $apiToken->created_at->format('d/m/Y à H:i') }}{{ $apiToken->last_used_at ? ', dernière utilisation le '.$apiToken->last_used_at->format('d/m/Y à H:i') : ', jamais utilisée' }}).
                Utilisez-la pour authentifier des requêtes vers <code>{{ url('/api/v1') }}</code> (stades, réservations) via l'en-tête <code>Authorization: Bearer &lt;clé&gt;</code>.
                L'accès est automatiquement limité à ce que votre compte peut voir dans l'application.
            </p>
            <div style="display:flex;gap:8px">
                <form method="POST" action="{{ route('parametres.api-token.generate') }}" onsubmit="return confirm('Régénérer la clé API ? L\'ancienne cessera de fonctionner immédiatement.');">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-rotate"></i> Régénérer</button>
                </form>
                <form method="POST" action="{{ route('parametres.api-token.revoke') }}" onsubmit="return confirm('Révoquer la clé API ? Les intégrations qui l\'utilisent cesseront de fonctionner.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" style="color:#dc2626;border-color:#fecaca"><i class="fa-solid fa-trash"></i> Révoquer</button>
                </form>
            </div>
        @else
            <p style="color:#64748b;font-size:14px;margin-bottom:16px">Aucune clé API générée pour le moment. Créez-en une pour permettre à une intégration externe d'accéder à la plateforme en votre nom.</p>
            <form method="POST" action="{{ route('parametres.api-token.generate') }}">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-key"></i> Générer une clé API</button>
            </form>
        @endif
    </div>
</div>

@endsection
