<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnedPitches;
use App\Http\Controllers\Controller;
use App\Http\Resources\PitchResource;
use App\Models\Pitch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PitchController extends Controller
{
    use ScopesToOwnedPitches;

    public function index(Request $request): AnonymousResourceCollection
    {
        $pitchIds = $this->visiblePitchIds($request->user());

        $pitches = Pitch::with('owner')
            ->when($pitchIds !== null, fn ($q) => $q->whereIn('id', $pitchIds))
            ->orderBy('city')
            ->paginate(20);

        return PitchResource::collection($pitches);
    }

    public function show(Request $request, Pitch $pitch): PitchResource
    {
        $pitchIds = $this->visiblePitchIds($request->user());

        abort_if($pitchIds !== null && ! $pitchIds->contains($pitch->id), 403);

        return new PitchResource($pitch->load('owner'));
    }
}
