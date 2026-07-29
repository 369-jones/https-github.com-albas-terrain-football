<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    // ── Nouvelle réservation ──────────────────────
    public static function nouvelleReservation(string $equipeA, string $equipeB, string $date): void
    {
        Notification::create([
            'titre' => 'Nouvelle réservation',
            'message' => "Match programmé : {$equipeA} vs {$equipeB} le {$date}",
            'type' => 'info',
            'categorie' => 'reservation',
            'lien' => '/reservations',
        ]);
    }

    // ── Réservation confirmée ─────────────────────
    public static function reservationConfirmee(string $equipeA, string $equipeB): void
    {
        Notification::create([
            'titre' => 'Réservation confirmée ✅',
            'message' => "La réservation {$equipeA} vs {$equipeB} a été confirmée.",
            'type' => 'success',
            'categorie' => 'reservation',
            'lien' => '/reservations',
        ]);
    }

    // ── Réservation annulée ───────────────────────
    public static function reservationAnnulee(string $equipeA, string $equipeB): void
    {
        Notification::create([
            'titre' => 'Réservation annulée',
            'message' => "La réservation {$equipeA} vs {$equipeB} a été annulée.",
            'type' => 'warning',
            'categorie' => 'reservation',
            'lien' => '/reservations',
        ]);
    }

    // ── Paiement reçu ─────────────────────────────
    public static function paiementRecu(string $equipeA, string $montant, string $devise): void
    {
        Notification::create([
            'titre' => 'Paiement reçu 💰',
            'message' => "Paiement de {$montant} {$devise} reçu de l'équipe {$equipeA}.",
            'type' => 'success',
            'categorie' => 'paiement',
            'lien' => '/paiements',
        ]);
    }

    // ── Paiement partiel ──────────────────────────
    public static function paiementPartiel(string $equipeA, string $montant, string $devise): void
    {
        Notification::create([
            'titre' => 'Paiement partiel ⚠️',
            'message' => "Paiement partiel de {$montant} {$devise} reçu de {$equipeA}.",
            'type' => 'warning',
            'categorie' => 'paiement',
            'lien' => '/paiements',
        ]);
    }

    // ── Impayé détecté ────────────────────────────
    public static function impayeDetecte(string $equipeA, string $montant): void
    {
        Notification::create([
            'titre' => 'Impayé détecté ❌',
            'message' => "L'équipe {$equipeA} a un impayé de {$montant}.",
            'type' => 'danger',
            'categorie' => 'paiement',
            'lien' => '/paiements',
        ]);
    }

    // ── Facture générée ───────────────────────────
    public static function factureGeneree(string $numero, string $equipeA): void
    {
        Notification::create([
            'titre' => 'Facture générée 🧾',
            'message' => "La facture {$numero} a été générée pour {$equipeA}.",
            'type' => 'success',
            'categorie' => 'facture',
            'lien' => '/factures',
        ]);
    }

    // ── Nouvelle équipe ───────────────────────────
    public static function nouvelleEquipe(string $nom): void
    {
        Notification::create([
            'titre' => 'Nouvelle équipe inscrite 👥',
            'message' => "L'équipe {$nom} vient de s'inscrire.",
            'type' => 'info',
            'categorie' => 'equipe',
            'lien' => '/equipes',
        ]);
    }
}
