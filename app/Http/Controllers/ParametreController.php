<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParametreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $apiToken = $user->tokens()->latest()->first();

        return view('parametres.index', compact('user', 'apiToken'));
    }

    public function generateApiToken(Request $request)
    {
        $user = Auth::user();

        // Single-key model: regenerating replaces whatever key existed before.
        $user->tokens()->delete();
        $plainTextToken = $user->createToken('parametres')->plainTextToken;

        return redirect()->route('parametres')
            ->with('success', 'Nouvelle clé API générée — copiez-la maintenant, elle ne sera plus affichée.')
            ->with('new_api_token', $plainTextToken);
    }

    public function revokeApiToken(Request $request)
    {
        Auth::user()->tokens()->delete();

        return redirect()->route('parametres')->with('success', 'Clé API révoquée.');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('parametres')
            ->with('success', 'Paramètres mis à jour avec succès !');
    }
}
