<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleService $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = app(ArticleService::class);
    }

    public function test_can_store_article(): void
    {
        $data = [
            'external_id' => 'test_123',
            'source' => 'newsapi',
            'title' => 'Test Article',
            'published_at' => now(),
        ];

        $article = $this->articleService->storeArticle($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Test Article', $article->title);
    }

    public function test_can_filter_articles_by_source(): void
    {
        Article::factory()->create(['source' => 'newsapi']);
        Article::factory()->create(['source' => 'theguardian']);

        $articles = $this->articleService->getArticles(['sources' => ['newsapi']], 10);

        $this->assertCount(1, $articles);
    }

    public function test_can_get_available_sources(): void
    {
        Article::factory()->create(['source' => 'newsapi']);
        Article::factory()->create(['source' => 'theguardian']);

        $sources = $this->articleService->getAvailableSources();

        $this->assertCount(2, $sources);
        $this->assertContains('newsapi', $sources);
    }

}