<?php

namespace App\Helpers;

class DeviseHelper
{
    /**
     * Formate un montant avec sa devise.
     */
    public static function formater(?float $montant, string $devise = 'USD'): string
    {
        // Sécurité : montant null → 0
        if (is_null($montant)) {
            $montant = 0;
        }

        $devises = config('devises.liste');
        $info = $devises[$devise] ?? $devises['USD'];

        $montantFormate = number_format(
            $montant,
            $info['decimales'],
            ',',
            $info['separateur']
        );

        // Devises avec symbole avant le montant ($, £, €)
        $symbolesAvant = ['USD', 'GBP', 'CAD'];

        if (in_array($devise, $symbolesAvant)) {
            return $info['symbole'].' '.$montantFormate;
        }

        // Toutes les autres : montant puis symbole
        return $montantFormate.' '.$info['symbole'];
    }

    /**
     * Retourne le symbole d'une devise.
     */
    public static function symbole(string $devise = 'USD'): string
    {
        $devises = config('devises.liste');

        return $devises[$devise]['symbole'] ?? $devise;
    }

    /**
     * Retourne le drapeau d'une devise.
     */
    public static function drapeau(string $devise = 'USD'): string
    {
        $devises = config('devises.liste');

        return $devises[$devise]['drapeau'] ?? '🌍';
    }

    /**
     * Retourne toutes les devises pour un select.
     */
    public static function liste(): array
    {
        return config('devises.liste');
    }
}
