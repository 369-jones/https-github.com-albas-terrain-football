<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FactureResource;
use App\Models\Facture;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

// Club-wide back-office resource, gated to role:admin (routes/api.php). Factures are
// entirely generated as a side effect of Paiement::record() — the web FactureController's
// create/store/edit/update are all no-op redirects, so only index/show/destroy/pdf exist here.
class FactureController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return FactureResource::collection(
            Facture::with(['reservation.equipeA', 'reservation.equipeB', 'paiement'])->latest()->paginate(20)
        );
    }

    public function show(Facture $facture): FactureResource
    {
        return new FactureResource($facture->load('reservation.equipeA', 'reservation.equipeB', 'paiement'));
    }

    public function destroy(Facture $facture): Response
    {
        $facture->delete();

        return response()->noContent();
    }

    public function pdf(Facture $facture): SymfonyResponse
    {
        $facture->load('reservation.equipeA', 'reservation.equipeB', 'paiement');

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('factures.pdf', compact('facture'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ])
            ->download($facture->numero_facture.'.pdf');
    }
}
