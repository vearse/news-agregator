<?php

namespace App\Services\NewsAggregator\Sources;

use App\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseNewsSource implements NewsSourceInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout = 30;

    abstract protected function buildRequestUrl(): string;
    abstract protected function parseResponse(array $response): array;

    public function fetch(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->buildRequestUrl());

            if ($response->failed()) {
                Log::error("Failed to fetch from {$this->getSource()}", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $articles = $this->parseResponse($response->json());

            return array_map(fn($article) => $this->transform($article), $articles);

        } catch (\Exception $e) {
            Log::error("Error fetching from {$this->getSource()}: {$e->getMessage()}");
            return [];
        }
    }

    protected function generateExternalId(array $article): string
    {
        $identifier = $article['url'] ?? $article['title'] ?? uniqid();
        return $this->getSource() . '_' . md5($identifier);
    }
}