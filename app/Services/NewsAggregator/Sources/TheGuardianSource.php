<?php

namespace App\Services\NewsAggregator\Sources;

use App\Enums\NewsSource;
use Carbon\Carbon;


class TheGuardianSource extends BaseNewsSource
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com';
        $this->apiKey = config('services.theguardian.key');
    }

    public function getSource(): string
    {
        return NewsSource::THE_GUARDIAN->value;
    }

    protected function buildRequestUrl(): string
    {
        $params = http_build_query([
            'api-key' => $this->apiKey,
            'show-fields' => 'thumbnail,trailText,bodyText,byline',
            'page-size' => 25, //Abstract
            'from-date' => Carbon::now()->subDay()->toDateString(),
            'order-by' => 'newest',
        ]);

        // \Log::info(['Guardian NEWS DOCS with params', "{$this->baseUrl}/search?{$params}" ]);

        return "{$this->baseUrl}/search?{$params}";
    }

    protected function parseResponse(array $response): array
    {
        return $response['response']['results'] ?? [];
    }

    public function transform(array $article): array
    {
        $fields = $article['fields'] ?? [];

        return [
            'external_id' => $this->generateExternalId($article),
            'source' => $this->getSource(),
            'title' => $article['webTitle'] ?? 'Untitled',
            'description' => $fields['trailText'] ?? null,
            'content' => $fields['bodyText'] ?? null,
            'author' => $fields['byline'] ?? 'Unknown',
            'category' => $article['sectionName'] ?? 'general',
            'url' => $article['webUrl'] ?? null,
            'image_url' => $fields['thumbnail'] ?? null,
            'published_at' => isset($article['webPublicationDate']) 
                ? Carbon::parse($article['webPublicationDate']) 
                : Carbon::now(),
        ];
    }
}