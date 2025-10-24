<?php

namespace App\Contracts;

interface NewsSourceInterface
{
    /**
     * Fetch articles from the news source
     *
     * @return array
     */
    public function fetch(): array;

    /**
     * Get the source identifier
     *
     * @return string
     */
    public function getSource(): string;

    /**
     * Transform raw article data to standardized format
     *
     * @param array $article
     * @return array
     */
    public function transform(array $article): array;
}