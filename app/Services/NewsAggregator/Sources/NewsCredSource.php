<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;


class NewsCredSource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
   public function __construct()
    {
        $this->baseUrl = 'https://api.newscred.com/v1'; // Replace with actual NewsCred API endpoint
        $this->apiKey = config('services.newscred.key');
    }

    public function getSource(): string
    {
        return NewsSource::NEWS_CRED->value;
    }

    protected function buildRequestUrl(): string
{
        $params = http_build_query([
            'access_key' => $this->apiKey,
            'limit' => 50,
            'from_date' => Carbon::now()->subDay()->toDateString(),
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
            'description' => $article['description'] ?? null,
            'content' => $article['content'] ?? null,
            'author' => $article['author']['name'] ?? 'Unknown',
            'category' => $article['categories'][0] ?? 'general',
            'url' => $article['source_url'] ?? null,
            'image_url' => $article['image_url'] ?? null,
            'published_at' => isset($article['published_at']) 
                ? Carbon::parse($article['published_at']) 
                : Carbon::now(),
        ];
    }
}