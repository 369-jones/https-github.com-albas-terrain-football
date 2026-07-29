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
     *
     * Provider: open.er-api.com (exchangerate-api.com's free "open" endpoint) — no API
     * key, updates once/day, and actually carries CDF (unlike several free providers).
     * api.exchangerate.host used to work here too but now requires a paid key — if you
     * swap providers again, verify with a raw curl first, don't assume the schema matches.
     */
    public function rates(): array
    {
        return Cache::remember(
            config('currencies.rate_cache_key'),
            now()->addHours(config('currencies.rate_cache_ttl_hours')),
            function () {
                $base = config('currencies.base');

                try {
                    $response = Http::timeout(5)->get("https://open.er-api.com/v6/latest/{$base}");

                    if ($response->successful() && $response->json('result') === 'success') {
                        $rates = $response->json('rates', []);

                        if (! empty($rates)) {
                            return $rates;
                        }
                    }

                    Log::warning('Exchange rate API returned an unusable response.', ['body' => $response->body()]);
                } catch (\Throwable $e) {
                    Log::warning('Exchange rate fetch failed: '.$e->getMessage());
                }

                return $this->fallbackRates($base);
            }
        );
    }

    /**
     * Used only if the live API is unreachable. Identity (1:1) is a reasonable fallback
     * for currencies close in value, but USD/CDF differ by three orders of magnitude —
     * falling back to 1:1 there would show a $15/hr pitch as 15 CDF/hr, wildly misleading
     * rather than just stale. These are approximate mid-2026 rates, not live figures.
     */
    private function fallbackRates(string $base): array
    {
        $approxToUsd = [
            'USD' => 1,
            'CDF' => 2270,
        ];

        $baseRate = $approxToUsd[$base] ?? 1;

        return collect($approxToUsd)
            ->mapWithKeys(fn ($rate, $currency) => [$currency => $rate / $baseRate])
            ->all();
    }
}
