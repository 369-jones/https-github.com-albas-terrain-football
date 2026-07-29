<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EquipeController as WebEquipeController;
use App\Http\Resources\EquipeResource;
use App\Models\Equipe;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

// Club-wide back-office resource, not a stadium — gated to role:admin at the route
// level (see routes/api.php) same as the web Equipes controller, no owner scoping.
class EquipeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return EquipeResource::collection(Equipe::withCount('reservations')->latest()->paginate(20));
    }

    public function show(Equipe $equipe): EquipeResource
    {
        return new EquipeResource($equipe->load('reservations.equipeA', 'reservations.equipeB'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(WebEquipeController::rules());

        $equipe = Equipe::create($validated);

        NotificationService::nouvelleEquipe($equipe->nom);

        return (new EquipeResource($equipe))->response()->setStatusCode(201);
    }

    public function update(Request $request, Equipe $equipe): EquipeResource
    {
        $validated = $request->validate(WebEquipeController::rules($equipe->id));

        $equipe->update($validated);

        return new EquipeResource($equipe->fresh());
    }

    public function destroy(Equipe $equipe): Response
    {
        $equipe->delete();

        return response()->noContent();
    }
}
