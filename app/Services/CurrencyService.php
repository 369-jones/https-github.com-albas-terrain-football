<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Convert an amount from one currency to another using cached exchange rates.
     */
    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rates = $this->rates();
        $base = config('currencies.base');

        // Convert "from" -> base -> "to"
        $amountInBase = $from === $base ? $amount : $amount / ($rates[$from] ?? 1);
        $converted = $to === $base ? $amountInBase : $amountInBase * ($rates[$to] ?? 1);

        return round($converted, config("currencies.supported.$to.decimals", 2));
    }

    public function format(float $amount, string $currency): string
    {
        $meta = config("currencies.supported.$currency");
        $decimals = $meta['decimals'] ?? 2;
        $symbol = $meta['symbol'] ?? $currency;

        return number_format($amount, $decimals, ',', ' ').' '.$symbol;
    }

    /**
     * Exchange rates relative to the base currency, cached for
     * config('currencies.rate_cache_ttl_hours') hours.
     * Swap the provider below for whichever exchange-rate API you use in production.
     */
    public function rates(): array
    {
        return Cache::remember(
            config('currencies.rate_cache_key'),
            now()->addHours(config('currencies.rate_cache_ttl_hours')),
            function () {
                try {
                    $base = config('currencies.base');
                    $response = Http::timeout(5)->get("https://api.exchangerate.host/latest", [
                        'base' => $base,
                    ]);

                    if ($response->successful()) {
                        return $response->json('rates', []);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Exchange rate fetch failed: '.$e->getMessage());
                }

                // Fallback: identity rates so the app keeps working if the API is down.
                return array_fill_keys(array_keys(config('currencies.supported')), 1);
            }
        );
    }
}
