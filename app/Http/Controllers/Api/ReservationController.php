<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

// Club-wide back-office resource, gated to role:admin at the route level (routes/api.php),
// same as the web Reservations controller — no owner/stadium scoping here.
class ReservationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ReservationResource::collection(
            Reservation::with(['equipeA', 'equipeB', 'paiement'])->latest()->paginate(20)
        );
    }

    public function show(Reservation $reservation): ReservationResource
    {
        return new ReservationResource($reservation->load('equipeA', 'equipeB', 'paiement'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date|after_or_equal:today',
            'creneau' => 'required|in:'.implode(',', Reservation::CRENEAUX),
            'type_match' => 'required|in:'.implode(',', Reservation::TYPES_MATCH),
            'montant' => 'required|numeric|min:0',
            'devise' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $reservation = Reservation::bookSlot($validated);

        return (new ReservationResource($reservation->load('equipeA', 'equipeB')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Reservation $reservation): ReservationResource
    {
        $validated = $request->validate([
            'equipe_a_id' => 'required|exists:equipes,id',
            'equipe_b_id' => 'required|exists:equipes,id|different:equipe_a_id',
            'date_match' => 'required|date',
            'creneau' => 'required|in:'.implode(',', Reservation::CRENEAUX),
            'type_match' => 'required|in:'.implode(',', Reservation::TYPES_MATCH),
            'montant' => 'required|numeric|min:0',
            'devise' => 'required|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $reservation->update($validated);

        return new ReservationResource($reservation->fresh(['equipeA', 'equipeB']));
    }

    /**
     * Cancels the reservation (statut = annule), matching the web controller's destroy()
     * exactly — reservations are never hard-deleted, since Paiement/Facture rows key off them.
     */
    public function destroy(Reservation $reservation): Response
    {
        $reservation->cancel();

        return response()->noContent();
    }
}
