<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Pitch;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService): View
    {
        $ownerId = $request->user()->hasRole('admin') ? null : $request->user()->id;
        $currency = session('currency', config('currencies.default'));

        $pitches = Pitch::when($ownerId, fn ($q) => $q->where('owner_id', $ownerId))
            ->orderBy('city')->get(['id', 'name', 'city']);

        $payments = $this->filteredQuery($request, $ownerId)
            ->with(['booking.pitch', 'booking.user'])
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        // Summaries need every matching payment (not just the current page), converted into
        // one currency for comparison — conversion has to happen in PHP since exchange rates
        // aren't known to SQL, same reasoning as the marketplace price filters.
        $allMatching = $this->filteredQuery($request, $ownerId)->with('booking.pitch')->get();
        $convert = fn (Payment $p) => $currencyService->convert((float) $p->amount, $p->currency, $currency);

        $successful = $allMatching->where('status', 'successful');

        $summary = [
            'total_earned' => $successful->sum($convert),
            'this_month' => $successful->filter(fn (Payment $p) => $p->paid_at?->isCurrentMonth())->sum($convert),
            'pending' => $allMatching->where('status', 'pending')->sum($convert),
            'refunded' => $allMatching->where('status', 'refunded')->sum($convert),
        ];

        $byPitch = $successful
            ->groupBy('booking.pitch_id')
            ->map(function (Collection $group) use ($convert) {
                $pitch = $group->first()->booking->pitch;

                return [
                    'pitch' => $pitch,
                    'count' => $group->count(),
                    'total' => $group->sum($convert),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return view('admin.earnings.index', compact('payments', 'summary', 'byPitch', 'pitches', 'currency'));
    }

    public function export(Request $request): Response
    {
        $ownerId = $request->user()->hasRole('admin') ? null : $request->user()->id;

        $payments = $this->filteredQuery($request, $ownerId)
            ->with(['booking.pitch', 'booking.user'])
            ->latest('created_at')
            ->get();

        $rows = ["Date,Pitch,Player,Provider,Reference,Amount,Currency,Status"];
        foreach ($payments as $payment) {
            $rows[] = implode(',', [
                $payment->created_at->format('Y-m-d H:i'),
                $this->csvField($payment->booking->pitch->nameFor('en')),
                $this->csvField($payment->booking->user->name),
                $payment->provider,
                $payment->reference,
                $payment->amount,
                $payment->currency,
                $payment->status,
            ]);
        }

        return response(implode("\n", $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="earnings-'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    private function csvField(string $value): string
    {
        return '"'.str_replace('"', '""', $value).'"';
    }

    private function filteredQuery(Request $request, ?int $ownerId): Builder
    {
        return Payment::query()
            ->when($ownerId, fn ($q) => $q->whereHas('booking.pitch', fn ($qq) => $qq->where('owner_id', $ownerId)))
            ->when($request->filled('pitch_id'), function ($q) use ($request) {
                $q->whereHas('booking', fn ($b) => $b->where('pitch_id', $request->integer('pitch_id')));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('provider'), fn ($q) => $q->where('provider', $request->input('provider')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));
    }
}
