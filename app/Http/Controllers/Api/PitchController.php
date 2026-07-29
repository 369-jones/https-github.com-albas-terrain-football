<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ScopesToOwnedPitches;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PitchController as WebPitchController;
use App\Http\Resources\PitchResource;
use App\Models\Pitch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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

    /**
     * Same validation rules as the web admin form (PitchController::validatePitch),
     * shared rather than duplicated so the two never drift apart.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasAnyRole(['owner', 'admin']), 403);

        $validated = WebPitchController::validatePitch($request);

        $pitch = Pitch::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'slug' => Pitch::uniqueSlug($validated['name']['en'] ?? $validated['name']['fr']),
        ]);

        return (new PitchResource($pitch->load('owner')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Pitch $pitch): PitchResource
    {
        $this->authorizePitchManagement($pitch, $request->user());

        $validated = WebPitchController::validatePitch($request);

        $pitch->update($validated);

        return new PitchResource($pitch->fresh('owner'));
    }

    public function destroy(Request $request, Pitch $pitch): Response
    {
        $this->authorizePitchManagement($pitch, $request->user());

        $pitch->delete();

        return response()->noContent();
    }
}
