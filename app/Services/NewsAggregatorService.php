<?php

namespace App\Services;

use App\Contracts\NewsSourceInterface;
use App\Services\NewsAggregator\Sources\NewsAPISource;
use App\Services\NewsAggregator\Sources\TheGuardianSource;
use App\Services\NewsAggregator\Sources\BBCNewsSource;
use App\Services\NewsAggregator\Sources\NewsCredSource;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    /** @var NewsSourceInterface[] */
    protected array $sources;
    
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
        $this->initializeSources();
    }

    protected function initializeSources(): void
    {
        $this->sources = [
            new NewsAPISource(),
            new TheGuardianSource(),
            new BBCNewsSource(),
            new NewsCredSource(),
        ];
    }

    /**
     * Fetch and store articles from all sources
     */
    public function aggregateNews(): array
    {
        $results = [];

        foreach ($this->sources as $source) {
            $source = $source->getSource();
            
            Log::info("Starting to fetch articles from {$source}");
            
            $articles = $source->fetch();
            $stored = $this->articleService->bulkStoreArticles($articles);
            
            $results[$source] = [
                'fetched' => count($articles),
                'stored' => $stored,
            ];
            
            Log::info("Completed fetching from {$source}", $results[$source]);
        }

        return $results;
    }

    /**
     * Fetch from specific source
     */
    public function aggregateFromSource(string $source): array
    {
        $source = $this->getNewsBySource($source);

        if (!$source) {
            throw new \InvalidArgumentException("Invalid source: {$source}");
        }

        $articles = $source->fetch();
        $stored = $this->articleService->bulkStoreArticles($articles);

        return [
            'source' => $source,
            'fetched' => count($articles),
            'stored' => $stored,
        ];
    }

    /**
     * Get source by source name
     */
    protected function getNewsBySource(string $source): ?NewsSourceInterface
    {
        foreach ($this->sources as $source) {
            if ($source->getSource() === $source) {
                return $source;
            }
        }

        return null;
    }
}

