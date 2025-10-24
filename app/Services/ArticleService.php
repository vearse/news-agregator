<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
   public function __construct(
        // protected CacheService $cacheService
    ) {}

    /**
     * Get articles with filters and pagination
     */
    public function getArticles(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query()
            ->search($filters['search'] ?? null)
            ->filterBySource($filters['sources'] ?? [])
            ->filterByCategory($filters['categories'] ?? [])
            ->filterByAuthor($filters['authors'] ?? [])
            ->filterByDateRange($filters['from'] ?? null, $filters['to'] ?? null)
            ->latest('published_at');

        return $query->paginate($perPage);
    }

    /**
     * Store or update article
     */
    public function storeArticle(array $data): Article
    {
        return Article::updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }

    /**
     * Bulk store articles
     */
    public function bulkStoreArticles(array $articles): int
    {
        $count = 0;

        foreach ($articles as $articleData) {
            try {
                $this->storeArticle($articleData);
                $count++;
            } catch (\Exception $e) {
                \Log::error("Failed to store article: {$e->getMessage()}", $articleData);
            }
        }

        return $count;
    }

    /**
     * Get available sources with caching
     */
    public function getAvailableSources(): array
    {
        return $this->cacheService->getCachedSources(function () {
            return Article::distinct()
                ->pluck('source')
                ->toArray();
        });
    }

    /**
     * Get available categories with caching
     */
    public function getAvailableCategories(): array
    {
        return $this->cacheService->getCachedCategories(function () {
            return Article::distinct()
                ->whereNotNull('category')
                ->pluck('category')
                ->toArray();
        });
    }

    /**
     * Get available authors with caching
     */
    public function getAvailableAuthors(): array
    {
        return $this->cacheService->getCachedAuthors(function () {
            return Article::distinct()
                ->whereNotNull('author')
                ->pluck('author')
                ->toArray();
        });
    }

}