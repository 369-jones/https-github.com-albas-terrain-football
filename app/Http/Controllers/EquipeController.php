<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class EquipeController extends Controller
{
    public function index()
    {
        $equipes = Equipe::withCount('reservations')->latest()->get();

        return view('equipes.index', compact('equipes'));
    }

    public function create()
    {
        return view('equipes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:equipes,nom',
            'responsable' => 'required|string|max:255',
            'contact' => 'required|string|max:50',
            'faculte' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ], [
            'nom.required' => 'Le nom de l\'équipe est obligatoire.',
            'nom.unique' => 'Ce nom d\'équipe existe déjà.',
            'responsable.required' => 'Le responsable est obligatoire.',
            'contact.required' => 'Le contact est obligatoire.',
        ]);

        Equipe::create($request->all());

        NotificationService::equipeCreee($request->nom);

        return redirect()->route('equipes.index')
            ->with('success', 'Équipe créée avec succès !');
    }

    public function show(Equipe $equipe)
    {
        $equipe->load('reservations.equipeA', 'reservations.equipeB');

        return view('equipes.show', compact('equipe'));
    }

    public function edit(Equipe $equipe)
    {
        return view('equipes.edit', compact('equipe'));
    }

    public function update(Request $request, Equipe $equipe)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:equipes,nom,'.$equipe->id,
            'responsable' => 'required|string|max:255',
            'contact' => 'required|string|max:50',
            'faculte' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $equipe->update($request->all());

        return redirect()->route('equipes.index')
            ->with('success', 'Équipe mise à jour avec succès !');
    }

    public function destroy(Equipe $equipe)
    {
        $equipe->delete();

        return redirect()->route('equipes.index')
            ->with('success', 'Équipe supprimée avec succès !');
    }
}
