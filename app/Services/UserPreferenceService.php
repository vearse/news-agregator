<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceService
{
    /**
     * Get user preferences
     */
    public function getUserPreferences(User $user): ?UserPreference
    {
        return $user->preference;
    }

    /**
     * Update or create user preferences
     */
    public function updatePreferences(User $user, array $data): UserPreference
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'sources' => $data['sources'] ?? null,
                'categories' => $data['categories'] ?? null,
                'authors' => $data['authors'] ?? null,
            ]
        );
    }

    /**
     * Apply user preferences to query filters
     */
    public function applyPreferencesToFilters(User $user, array $filters): array
    {
        $preference = $this->getUserPreferences($user);

        if (!$preference) {
            return $filters;
        }

        // Merge user preferences with provided filters
        if (!isset($filters['sources']) && $preference->sources) {
            $filters['sources'] = $preference->sources;
        }

        if (!isset($filters['categories']) && $preference->categories) {
            $filters['categories'] = $preference->categories;
        }

        if (!isset($filters['authors']) && $preference->authors) {
            $filters['authors'] = $preference->authors;
        }

        return $filters;
    }
}