<?php

namespace App\Http\Controllers;

use App\Models\Pitch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Platform-admin only: decides which staff account is responsible for each stadium.
// Deliberately keyed by pitch, not by user — the data model is one manager per stadium
// (Pitch::owner_id), so "assign a manager" and "reassign a stadium" are the same action.
class StaffController extends Controller
{
    public function index(): View
    {
        $pitches = Pitch::with('owner')->orderBy('sport')->orderBy('city')->get();

        return view('admin.staff.index', compact('pitches'));
    }

    public function edit(Pitch $pitch): View
    {
        $pitch->load('owner');

        return view('admin.staff.edit', compact('pitch'));
    }

    public function update(Request $request, Pitch $pitch): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['nullable', 'string', 'max:120'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            if (empty($validated['name']) || empty($validated['password'])) {
                return back()->withErrors([
                    'email' => __('No account exists with that email yet — provide a name and password to create one.'),
                ])->withInput();
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);
        } elseif (! empty($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }

        $previousOwnerId = $pitch->owner_id;

        $pitch->update(['owner_id' => $user->id]);
        $user->assignRole('owner');

        if ($previousOwnerId !== $user->id && Pitch::where('owner_id', $previousOwnerId)->doesntExist()) {
            User::find($previousOwnerId)?->removeRole('owner');
        }

        return redirect()->route('admin.staff.index')
            ->with('success', __(':name is now responsible for :pitch.', ['name' => $user->name, 'pitch' => $pitch->nameFor()]));
    }
}
