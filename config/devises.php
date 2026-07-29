<?php

return [
    // Narrowed to USD + Congolese Franc for now — add more back into this list
    // (see git history for the previous set) once other markets are live.
    'liste' => [
        'USD' => [
            'nom' => 'Dollar américain',
            'symbole' => '$',
            'drapeau' => '🇺🇸',
            'separateur' => ',',
            'decimales' => 2,
        ],
        'CDF' => [
            'nom' => 'Franc Congolais',
            'symbole' => 'FC',
            'drapeau' => '🇨🇩',
            'separateur' => '.',
            'decimales' => 0,
        ],
    ],

    'defaut' => 'USD',
];
