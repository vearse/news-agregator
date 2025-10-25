<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserPreferenceService $preferenceService
    ) {}

    /**
     * Display the user's preferences
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $preference = $this->preferenceService->getUserPreferences($request->user());

            if (!$preference) {
                $defaultPreference = (object)[
                    'id' => null,
                    'user_id' => $request->user()->id,
                    'sources' => [],
                    'categories' => [],
                    'authors' => [],
                    'updated_at' => null,
                ];

                return $this->successResponse(
                    new UserPreferenceResource($defaultPreference),
                    'User preferences retrieved successfully (defaults)'
                );
            }

            return $this->successResponse(
                new UserPreferenceResource($preference),
                'User preferences retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve preferences', 500);
        }
    }

    /**
     * Update the user's preferences
     */
    public function update(UpdateUserPreferenceRequest $request): JsonResponse
    {
        try {
            $preference = $this->preferenceService->updatePreferences(
                $request->user(),
                $request->validated()
            );

            return $this->successResponse(
                new UserPreferenceResource($preference),
                'User preferences updated successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update preferences', 500);
        }
    }
}