<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PitchBlock extends Model
{
    protected $fillable = ['pitch_id', 'date', 'reason'];

    protected $casts = [
        'date' => 'date',
    ];

    public function pitch(): BelongsTo
    {
        return $this->belongsTo(Pitch::class);
    }
}
