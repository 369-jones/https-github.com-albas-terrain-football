@extends('layouts.app')

@section('title', 'Connexion — Terrain Football Universitaire')

@section('styles')
<style>
    /* ─── RESET & BASE ─────────────────────────────── */
    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
    }

    /* ─── FOND ANIMÉ ────────────────────────────────── */
    .bg {
        position: fixed;
        inset: 0;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
        z-index: 0;
        overflow: hidden;
    }
    .bg::before {
        content: '';
        position: absolute;
        width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(37,99,235,0.15) 0%, transparent 70%);
        top: -100px; right: -100px;
        border-radius: 50%;
    }
    .bg::after {
        content: '';
        position: absolute;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(124,58,237,0.10) 0%, transparent 70%);
        bottom: -80px; left: -80px;
        border-radius: 50%;
    }

    /* ─── CARTE LOGIN ───────────────────────────────── */
    .login-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        padding: 16px;
    }
    .login-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 44px 40px 36px;
        box-shadow:
            0 25px 60px rgba(0,0,0,0.4),
            0 0 0 1px rgba(255,255,255,0.05);
        animation: slideUp 0.4s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ─── LOGO ──────────────────────────────────────── */
    .login-logo {
        text-align: center;
        margin-bottom: 32px;
    }
    .login-logo .ball-wrap {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 36px;
        margin: 0 auto 16px;
        box-shadow: 0 8px 24px rgba(37,99,235,0.35);
    }
    .login-logo h1 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.3px;
    }
    .login-logo p {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    /* ─── CHAMPS ────────────────────────────────────── */
    .field { margin-bottom: 18px; }
    .field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 7px;
    }
    .field .input-wrap { position: relative; }
    .field .input-icon {
        position: absolute;
        left: 12px; top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        pointer-events: none;
    }
    .field input {
        width: 100%;
        padding: 11px 14px 11px 40px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .field input:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
    }
    .field input::placeholder { color: #94a3b8; }
    .field input.is-invalid { border-color: #dc2626; }

    /* ─── ERREURS ───────────────────────────────────── */
    .error-msg {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
        text-align: center;
        animation: shake 0.3s ease;
    }
    @keyframes shake {
        0%,100% { transform: translateX(0); }
        25%      { transform: translateX(-6px); }
        75%      { transform: translateX(6px); }
    }

    /* ─── REMEMBER + BOUTON ─────────────────────────── */
    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 18px;
        font-size: 13px;
        color: #64748b;
        cursor: pointer;
    }
    .remember-row input[type="checkbox"] {
        width: 16px; height: 16px;
        accent-color: #2563eb;
        cursor: pointer;
    }
    .btn-login {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 14px rgba(37,99,235,0.35);
    }
    .btn-login:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
        box-shadow: 0 6px 20px rgba(37,99,235,0.45);
        transform: translateY(-1px);
    }

    /* ─── HINT ──────────────────────────────────────── */
    .credentials-hint {
        margin-top: 20px;
        padding: 12px 16px;
        background: #f0f9ff;
        border: 1px dashed #93c5fd;
        border-radius: 10px;
        font-size: 12px;
        color: #1e40af;
        text-align: center;
        line-height: 1.6;
    }
    .credentials-hint strong {
        display: block;
        margin-bottom: 4px;
        color: #1e3a8a;
    }

    /* ─── FOOTER ────────────────────────────────────── */
    .login-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 11.5px;
        color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="bg"></div>

<div class="login-wrapper">
    <div class="login-card">

        {{-- LOGO --}}
        <div class="login-logo">
            <div class="ball-wrap">⚽</div>
            <h1>Terrain Football Universitaire</h1>
            <p>Espace d'administration — Accès sécurisé</p>
        </div>

        {{-- MESSAGE D'ERREUR --}}
        @if ($errors->any())
            <div class="error-msg">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        {{-- FORMULAIRE --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="field">
                <label>Adresse e-mail</label>
                <div class="input-wrap">
                    <span class="input-icon">📧</span>
                    <input
                        type="email"
                        name="email"
                        placeholder="admin@universite.ci"
                        value="{{ old('email') }}"
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        autocomplete="email"
                        autofocus
                    >
                </div>
            </div>

            <div class="field">
                <label>Mot de passe</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        autocomplete="current-password"
                    >
                </div>
            </div>

            <label class="remember-row">
                <input type="checkbox" name="remember">
                Se souvenir de moi
            </label>

            <button type="submit" class="btn-login">
                Se connecter →
            </button>

        </form>

        {{-- HINT DEMO --}}
        <div class="credentials-hint">
            <strong>🔑 Identifiants de démonstration</strong>
            Email : admin@universite.ci<br>
            Mot de passe : Admin@2024
        </div>

    </div>

    <div class="login-footer">
        © 2024 Terrain Football Universitaire · Tous droits réservés
    </div>
</div>
@endsection
