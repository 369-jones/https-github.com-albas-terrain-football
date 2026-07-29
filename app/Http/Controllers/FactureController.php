<?php

namespace App\Http\Controllers;

use App\Models\Facture;

class FactureController extends Controller
{
    public function index()
    {
        $factures = Facture::with([
            'reservation.equipeA',
            'reservation.equipeB',
            'paiement',
        ])->latest()->get();

        return view('factures.index', compact('factures'));
    }

    public function show(Facture $facture)
    {
        $facture->load('reservation.equipeA', 'reservation.equipeB', 'paiement');

        return view('factures.show', compact('facture'));
    }

    public function pdf(Facture $facture)
    {
        $facture->load('reservation.equipeA', 'reservation.equipeB', 'paiement');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('factures.pdf', compact('facture'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->download($facture->numero_facture.'.pdf');
    }

    public function create()
    {
        return redirect()->route('factures.index');
    }

    public function store()
    {
        return redirect()->route('factures.index');
    }

    public function edit()
    {
        return redirect()->route('factures.index');
    }

    public function update()
    {
        return redirect()->route('factures.index');
    }

    public function destroy(Facture $facture)
    {
        $facture->delete();

        return redirect()->route('factures.index')
            ->with('success', 'Facture supprimée.');
    }
}
