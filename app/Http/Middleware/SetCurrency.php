<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('currencies.supported'));

        $currency = $request->query('currency')
            ?? session('currency')
            ?? optional($request->user())->preferred_currency
            ?? config('currencies.default');

        if (! in_array($currency, $supported, true)) {
            $currency = config('currencies.default');
        }

        session(['currency' => $currency]);
        app()->instance('currency', $currency);

        return $next($request);
    }
}
