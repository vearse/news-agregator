<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected const CACHE_TTL = 3600; 
    protected const SOURCES_CACHE_KEY = 'articles:sources';
    protected const CATEGORIES_CACHE_KEY = 'articles:categories';
    protected const AUTHORS_CACHE_KEY = 'articles:authors';

    /**
     * Get cached sources or fetch from database
     */
    public function getCachedSources(callable $callback): array
    {
        return Cache::remember(
            self::SOURCES_CACHE_KEY,
            self::CACHE_TTL,
            $callback
        );
    }

    /**
     * Get cached categories or fetch from database
     */
    public function getCachedCategories(callable $callback): array
    {
        return Cache::remember(
            self::CATEGORIES_CACHE_KEY,
            self::CACHE_TTL,
            $callback
        );
    }

    /**
     * Get cached authors or fetch from database
     */
    public function getCachedAuthors(callable $callback): array
    {
        return Cache::remember(
            self::AUTHORS_CACHE_KEY,
            self::CACHE_TTL,
            $callback
        );
    }

    /**
     * Clear all article-related caches
     */
    public function clearArticleCaches(): void
    {
        Cache::forget(self::SOURCES_CACHE_KEY);
        Cache::forget(self::CATEGORIES_CACHE_KEY);
        Cache::forget(self::AUTHORS_CACHE_KEY);
    }
}