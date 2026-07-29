<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PitchImage extends Model
{
    protected $fillable = ['pitch_id', 'path', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function pitch(): BelongsTo
    {
        return $this->belongsTo(Pitch::class);
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
