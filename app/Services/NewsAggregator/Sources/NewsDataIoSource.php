<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;

class NewsDataIoSource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://newsdata.io/api/1/news';
        $this->apiKey = config('services.newsdataio.key');
    }

    public function getSource(): string
    {
        return NewsSource::NEWS_DATA_IO->value;
    }

    protected function buildRequestUrl(): string
    {
       $params = http_build_query([
            'apikey' => $this->apiKey,
            'country' => 'us', 
            'language' => 'en',
            'size' => 10,
        ]);

        \Log::info(['NewsDataIO NEWS DOCS with params', "{$this->baseUrl}?{$params}" ]);
        return "{$this->baseUrl}?{$params}";
    }

    protected function parseResponse(array $response): array
    {
        return $response['results'] ?? [];
    }

    public function transform(array $article): array
    {
        return [
            'external_id' => $this->generateExternalId($article),
            'source' => $this->getSource(),
            'title' => $article['title'] ?? 'Untitled',
            'description' => $article['description'] ?? null,
            'content' => $article['content'] === 'ONLY AVAILABLE IN PAID PLANS' ? null : $article['content'] ?? null,
            'author' => $article['creator'][0] ?? $article['source']['name'] ?? 'Unknown',
            'category' => $article['category'][0] ?? 'general',
            'url' => $article['link'] ?? null, 
            'image_url' => !empty($article['image_url']) ? trim($article['image_url']) : null,
            'published_at' => isset($article['pubDate'])
                ? Carbon::parse($article['pubDate'])
                : Carbon::now(),
        ];
    }

    
}