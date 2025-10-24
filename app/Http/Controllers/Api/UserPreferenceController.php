<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
   public function __construct(
        protected UserPreferenceService $preferenceService
    ) {}

    /**
     * Display the user's preferences
     */
    public function show(Request $request): UserPreferenceResource
    {
        $preference = $this->preferenceService->getUserPreferences($request->user());

        if (!$preference) {
            return new UserPreferenceResource((object)[
                'id' => null,
                'user_id' => $request->user()->id,
                'sources' => [],
                'categories' => [],
                'authors' => [],
                'updated_at' => null,
            ]);
        }

        return new UserPreferenceResource($preference);
    }

    /**
     * Update the user's preferences
     */
    public function update(UpdateUserPreferenceRequest $request): UserPreferenceResource
    {
        $preference = $this->preferenceService->updatePreferences(
            $request->user(),
            $request->validated()
        );

        return new UserPreferenceResource($preference);
    }
}