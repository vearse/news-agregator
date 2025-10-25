<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_articles_list(): void
    {
        Article::factory()->count(5)->create();

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'source',
                            'title',
                            'description',
                            'author',
                            'category',
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_filter_articles_by_source(): void
    {
        Article::factory()->create(['source' => 'newsapi']);
        Article::factory()->create(['source' => 'theguardian']);

        $response = $this->getJson('/api/articles?sources[]=newsapi');

        $response->assertOk();
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals('newsapi', $data[0]['source']);
    }

    public function test_can_get_single_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $article->id,
                    'title' => $article->title,
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_article(): void
    {
        $response = $this->getJson('/api/articles/999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_get_available_sources(): void
    {
        Article::factory()->create(['source' => 'newsapi']);
        Article::factory()->create(['source' => 'theguardian']);

        $response = $this->getJson('/api/articles/meta/sources');

        $response->assertOk()
            ->assertJson(['success' => true]);
        
        $this->assertCount(2, $response->json('data'));
    }

    public function test_can_get_available_categories(): void
    {
        Article::factory()->create(['category' => 'technology']);
        Article::factory()->create(['category' => 'business']);

        $response = $this->getJson('/api/articles/meta/categories');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_applies_user_preferences_when_authenticated(): void
    {
        $user = User::factory()->create();
        
        UserPreference::create([
            'user_id' => $user->id,
            'sources' => ['newsapi'],
        ]);

        Article::factory()->create(['source' => 'newsapi']);
        Article::factory()->create(['source' => 'theguardian']);

        $response = $this->actingAs($user)
            ->getJson('/api/articles');

        $response->assertOk();
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_validates_invalid_date_range(): void
    {
        $response = $this->getJson('/api/articles?from=2024-02-01&to=2024-01-01');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['to']);
    }

    public function test_validates_per_page_limit(): void
    {
        $response = $this->getJson('/api/articles?per_page=101');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }
}