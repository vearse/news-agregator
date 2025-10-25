<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleIndexRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use App\Services\UserPreferenceService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ArticleService $articleService,
        protected UserPreferenceService $preferenceService
    ) {}

    /**
     * Display a listing of articles
     */
    public function index(ArticleIndexRequest $request): JsonResponse
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

        return $this->successResponse(
            ArticleResource::collection($articles)->response()->getData(true),
            'Articles retrieved successfully'
        );
    }

    /**
     * Display the specified article
     */
    public function show(int $id): JsonResponse
    {
        try {
            $article = $this->articleService->getArticles(['id' => $id], 1)->first();

            if (!$article) {
                return $this->notFoundResponse('Article not found');
            }

            return $this->successResponse(
                new ArticleResource($article),
                'Article retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve article', 500);
        }
    }

    /**
     * Get available sources
     */
    public function sources(): JsonResponse
    {
        try {
            $sources = $this->articleService->getAvailableSources();
            
            return $this->successResponse(
                $sources,
                'Sources retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve sources', 500);
        }
    }

    /**
     * Get available categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->articleService->getAvailableCategories();
            
            return $this->successResponse(
                $categories,
                'Categories retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve categories', 500);
        }
    }

    /**
     * Get available authors
     */
    public function authors(): JsonResponse
    {
        try {
            $authors = $this->articleService->getAvailableAuthors();
            
            return $this->successResponse(
                $authors,
                'Authors retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve authors', 500);
        }
    }
}