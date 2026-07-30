<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaiementResource;
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

// Club-wide back-office resource, gated to role:admin (routes/api.php), no owner scoping.
// Only index/show/store — matching the web PaiementController exactly, whose edit/update/
// destroy are all no-op redirects. A payment is "corrected" by recording a new one against
// the same reservation (Paiement::record() updates it in place), never edited directly.
class PaiementController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PaiementResource::collection(
            Paiement::with(['reservation.equipeA', 'reservation.equipeB'])->latest()->paginate(20)
        );
    }

    public function show(Paiement $paiement): PaiementResource
    {
        return new PaiementResource($paiement->load('reservation.equipeA', 'reservation.equipeB', 'facture'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'montant_paye' => 'required|numeric|min:1',
            'mode_paiement' => 'required|in:'.implode(',', Paiement::MODES_PAIEMENT),
            'date_paiement' => 'required|date',
            'reference' => 'nullable|string|max:255',
        ]);

        $paiement = Paiement::record($validated);

        return (new PaiementResource($paiement->load('reservation.equipeA', 'reservation.equipeB', 'facture')))
            ->response()
            ->setStatusCode(201);
    }
}
