<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;

class BBCNewsSource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://bbc-api.com/v1'; // Replace with actual BBC API endpoint
        $this->apiKey = config('services.bbc.key');
    }

    public function getSource(): string
    {
        return NewsSource::BBC_NEWS->value;
    }

    protected function buildRequestUrl(): string
    {
        $params = http_build_query([
            'apiKey' => $this->apiKey,
            'limit' => 25,
            'since' => Carbon::now()->subDay()->toIso8601String(),
        ]);

        return "{$this->baseUrl}/articles?{$params}";
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
            'description' => $article['summary'] ?? null,
            'content' => $article['body'] ?? null,
            'author' => $article['author'] ?? 'BBC News',
            'category' => $article['section'] ?? 'general',
            'url' => $article['link'] ?? null,
            'image_url' => $article['image'] ?? null,
            'published_at' => isset($article['publishedAt']) 
                ? Carbon::parse($article['publishedAt']) 
                : Carbon::now(),
        ];
    }
}