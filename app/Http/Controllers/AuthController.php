<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Login se fait désormais via une modale sur la page d'accueil, pas une page dédiée.
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();

            return redirect()->route(match (true) {
                $user->hasRole('admin') => 'dashboard',
                $user->hasRole('owner') => 'admin.dashboard',
                default => 'home',
            });
        }

        return redirect()->route('home', ['login' => 1]);
    }

    // Traiter le formulaire de login
    public function login(Request $request)
    {
        // Validation des champs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => __('Email address is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 6 characters.'),
        ]);

        // Tentative de connexion
        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            return redirect()->route(match (true) {
                $user->hasRole('admin') => 'dashboard',
                $user->hasRole('owner') => 'admin.dashboard',
                default => 'home',
            });
        }

        // Échec → retour avec erreur
        return back()->withErrors([
            'email' => __('Incorrect email or password.'),
        ])->withInput($request->only('email'));
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
