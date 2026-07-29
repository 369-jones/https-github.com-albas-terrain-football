<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class CalendrierController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['equipeA', 'equipeB'])
            ->where('statut', '!=', 'annule')
            ->get()
            ->groupBy('date_match');

        return view('calendrier.index', compact('reservations'));
    }
}
