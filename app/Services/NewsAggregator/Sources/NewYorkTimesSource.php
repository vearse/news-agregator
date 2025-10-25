<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;

class NewYorkTimesSource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';
        $this->apiKey = config('services.newyorktimes.key');
    }

    public function getSource(): string
    {
        return NewsSource::NEW_YORK_TIMES->value;
    }

    protected function buildRequestUrl(): string
    {
        $params = http_build_query([
            'api-key' => $this->apiKey,
            'begin_date' => Carbon::now()->subDay()->format('Ymd'),
            'end_date' => Carbon::now()->format('Ymd'),
            'page' => 0,
            'fq' => 'news_desk:("Politics" "National" "Business" "World")', // Filter for news articles
        ]);

        return "{$this->baseUrl}?{$params}";
    }

    protected function parseResponse(array $response): array
    {
        return $response['response']['docs'] ?? [];
    }

    public function transform(array $article): array
    {
        return [
            'external_id' => $this->generateExternalId($article),
            'source' => $this->getSource(),
            'title' => $article['headline']['main'] ?? 'Untitled',
            'description' => $article['snippet'] ?? null,
            'content' => $article['lead_paragraph'] ?? null,
            'author' => $this->extractAuthor($article) ?? 'New York Times',
            'category' => $this->extractCategory($article) ?? 'general',
            'url' => $article['web_url'] ?? null,
            'image_url' => $this->extractImageUrl($article),
            'published_at' => isset($article['pub_date']) 
                ? Carbon::parse($article['pub_date']) 
                : Carbon::now(),
        ];
    }

    private function extractAuthor(array $article): ?string
    {
        $byline = $article['byline']['original'] ?? null;
        if ($byline && strpos($byline, 'By ') === 0) {
            return substr($byline, 3);
        }
        return $byline;
    }

    private function extractCategory(array $article): string
    {
        $newsDesk = $article['news_desk'] ?? 'General';
        $categoryMap = [
            'Politics' => 'politics',
            'National' => 'national',
            'Business' => 'business',
            'World' => 'world',
            'Sports' => 'sports',
            'Arts' => 'entertainment',
            'Science' => 'science',
            'Health' => 'health',
        ];

        return $categoryMap[$newsDesk] ?? 'general';
    }

    private function extractImageUrl(array $article): ?string
    {
        $multimedia = $article['multimedia'] ?? [];
        
        foreach ($multimedia as $media) {
            if (isset($media['subtype']) && $media['subtype'] === 'xlarge') {
                return 'https://nytimes.com/' . ($media['url'] ?? null);
            }
        }

        // Fallback to first image
        if (!empty($multimedia[0]['url'])) {
            return 'https://nytimes.com/' . $multimedia[0]['url'];
        }

        return null;
    }
}