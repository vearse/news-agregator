<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;

class NewsAPISource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
   public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2';
        $this->apiKey = config('services.newsapi.key');
    }

    public function getSource(): string
    {
        return NewsSource::NEWS_API->value;
    }

    protected function buildRequestUrl(): string
    {
        $params = http_build_query([
            'apiKey' => $this->apiKey,
            'language' => 'en',
            'pageSize' => 100,
            'sortBy' => 'publishedAt',
            'from' => Carbon::now()->subDay()->toIso8601String(),
        ]);

        return "{$this->baseUrl}/top-headlines?{$params}";
    }       

    protected function parseResponse(array $response): array
    {
        return $response['articles'] ?? [];
    }

    public function transform(array $article): array
    {
        return [
            'external_id' => $this->generateExternalId($article),
            'source' => $this->getSource(),
            'title' => $article['title'] ?? 'Untitled',
            'description' => $article['description'] ?? null,
            'content' => $article['content'] ?? null,
            'author' => $article['author'] ?? 'Unknown',
            'category' => $article['category'] ?? 'general',
            'url' => $article['url'] ?? null,
            'image_url' => $article['urlToImage'] ?? null,
            'published_at' => isset($article['publishedAt']) 
                ? Carbon::parse($article['publishedAt']) 
                : Carbon::now(),
        ];
    }
}
