<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(
        protected ArticleService $articleService,
        protected UserPreferenceService $preferenceService
    ) {}

    /**
     * Display a listing of articles
     */
    public function index(ArticleIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->filters();

        // Apply user preferences if authenticated
        if ($request->user()) {
            $filters = $this->preferenceService->applyPreferencesToFilters(
                $request->user(),
                $filters
            );
        }

        $perPage = $request->input('per_page', 15);
        $articles = $this->articleService->getArticles($filters, $perPage);

        return ArticleResource::collection($articles);
    }

    /**
     * Display the specified article
     */
    public function show(int $id): ArticleResource
    {
        $article = $this->articleService->getArticles(['id' => $id], 1)->first();

        if (!$article) {
            abort(404, 'Article not found');
        }

        return new ArticleResource($article);
    }

    /**
     * Get available sources
     */
    public function sources(): JsonResponse
    {
        return response()->json([
            'data' => $this->articleService->getAvailableSources()
        ]);
    }

    /**
     * Get available categories
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->articleService->getAvailableCategories()
        ]);
    }

    /**
     * Get available authors
     */
    public function authors(): JsonResponse
    {
        return response()->json([
            'data' => $this->articleService->getAvailableAuthors()
        ]);
    }
}
