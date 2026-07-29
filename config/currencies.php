<?php

return [
    'supported' => [
        'XOF' => ['name' => 'Franc CFA (UEMOA)', 'symbol' => 'CFA', 'decimals' => 0, 'countries' => ['CI', 'SN', 'ML', 'BF', 'BJ', 'TG', 'NE', 'GW']],
        'XAF' => ['name' => 'Franc CFA (CEMAC)', 'symbol' => 'FCFA', 'decimals' => 0, 'countries' => ['CM', 'GA', 'CG', 'TD', 'CF', 'GQ']],
        'NGN' => ['name' => 'Naira', 'symbol' => '₦', 'decimals' => 2, 'countries' => ['NG']],
        'GHS' => ['name' => 'Cedi', 'symbol' => 'GH₵', 'decimals' => 2, 'countries' => ['GH']],
        'KES' => ['name' => 'Shilling kenyan', 'symbol' => 'KSh', 'decimals' => 2, 'countries' => ['KE']],
        'TZS' => ['name' => 'Shilling tanzanien', 'symbol' => 'TSh', 'decimals' => 2, 'countries' => ['TZ']],
        'UGX' => ['name' => 'Shilling ougandais', 'symbol' => 'USh', 'decimals' => 0, 'countries' => ['UG']],
        'ZAR' => ['name' => 'Rand', 'symbol' => 'R', 'decimals' => 2, 'countries' => ['ZA']],
        'MAD' => ['name' => 'Dirham marocain', 'symbol' => 'DH', 'decimals' => 2, 'countries' => ['MA']],
        'EGP' => ['name' => 'Livre égyptienne', 'symbol' => 'E£', 'decimals' => 2, 'countries' => ['EG']],
        'CDF' => ['name' => 'Franc congolais', 'symbol' => 'FC', 'decimals' => 2, 'countries' => ['CD']],
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'decimals' => 2, 'countries' => []],
    ],

    // Base currency all internal accounting/exchange-rate math is anchored to.
    'base' => 'XOF',

    'default' => 'XOF',

    // Exchange rates are volatile — pull them from a live API (e.g. exchangerate.host,
    // openexchangerates.org) on a scheduled job (see App\Console\Commands\UpdateExchangeRates)
    // and cache them. Never hardcode rates in production.
    'rate_cache_key' => 'currency_exchange_rates',
    'rate_cache_ttl_hours' => 12,
];
