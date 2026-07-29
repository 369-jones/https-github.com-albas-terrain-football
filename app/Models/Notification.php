<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'titre',
        'message',
        'type',
        'categorie',
        'lien',
        'lue',
        'lue_at',
    ];

    protected $casts = [
        'lue' => 'boolean',
        'lue_at' => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────
    public function scopeNonLues($query)
    {
        return $query->where('lue', false);
    }

    public function scopeRecentes($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ── Icône selon le type ───────────────────────
    public function icone(): string
    {
        return match ($this->type) {
            'success' => '✅',
            'warning' => '⚠️',
            'danger' => '❌',
            default => 'ℹ️',
        };
    }

    // ── Couleur selon le type ─────────────────────
    public function couleur(): string
    {
        return match ($this->type) {
            'success' => '#15803d',
            'warning' => '#c2410c',
            'danger' => '#b91c1c',
            default => '#1e40af',
        };
    }

    // ── Background selon le type ──────────────────
    public function background(): string
    {
        return match ($this->type) {
            'success' => '#f0fdf4',
            'warning' => '#fff7ed',
            'danger' => '#fef2f2',
            default => '#eff6ff',
        };
    }

    // ── Border selon le type ──────────────────────
    public function border(): string
    {
        return match ($this->type) {
            'success' => '#bbf7d0',
            'warning' => '#fdba74',
            'danger' => '#fecaca',
            default => '#bfdbfe',
        };
    }

    // ── Marquer comme lue ─────────────────────────
    public function marquerLue(): void
    {
        $this->update([
            'lue' => true,
            'lue_at' => now(),
        ]);
    }
}
