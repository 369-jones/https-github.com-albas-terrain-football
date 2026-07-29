<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Pitch;
use App\Models\User;
use Illuminate\Support\Collection;

// Mirrors the scoping used by the web owner-dashboard controllers: the platform
// admin can see every stadium, everyone else only the ones they're responsible for.
trait ScopesToOwnedPitches
{
    private function visiblePitchIds(User $user): ?Collection
    {
        if ($user->hasRole('admin')) {
            return null;
        }

        return Pitch::where('owner_id', $user->id)->pluck('id');
    }
}
