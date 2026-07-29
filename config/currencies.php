<?php

return [
    // Narrowed to USD + Congolese Franc for now — add more back into this list
    // (see git history for the previous pan-African set) once other markets are live.
    'supported' => [
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'decimals' => 2, 'countries' => []],
        'CDF' => ['name' => 'Franc congolais', 'symbol' => 'FC', 'decimals' => 2, 'countries' => ['CD']],
    ],

    // Base currency all internal accounting/exchange-rate math is anchored to.
    'base' => 'USD',

    'default' => 'USD',

    // Exchange rates are volatile — pull them from a live API (e.g. exchangerate.host,
    // openexchangerates.org) on a scheduled job (see App\Console\Commands\UpdateExchangeRates)
    // and cache them. Never hardcode rates in production.
    'rate_cache_key' => 'currency_exchange_rates',
    'rate_cache_ttl_hours' => 12,
];
