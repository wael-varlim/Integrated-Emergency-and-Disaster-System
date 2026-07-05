<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Resources\AwarenessArticleResource;
use App\Models\AwarenessArticle;
use App\Services\AwarenessArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AwarenessArticleController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AwarenessArticleService $awarenessArticleService
    ) {}

    /**
     * Display a listing of awareness articles.
     */
    public function index(Request $request): JsonResponse
    {
        $newsTypeId = $request->query('news_type_id');

        if ($newsTypeId) {
            $articles = $this->awarenessArticleService->getArticlesByType((int)$newsTypeId);
        } else {
            $articles = $this->awarenessArticleService->getAllArticles();
        }

        return $this->apiResponse(
            [
                'articles' => AwarenessArticleResource::collection($articles),
            ],
            __('awareness.fetched_successfully'),
            200
        );
    }

    /**
     * Display the specified awareness article.
     */
    public function show(int $id): JsonResponse
    {
        $article = $this->awarenessArticleService->getArticleById($id);

        return $this->apiResponse(
            [
                'article' => new AwarenessArticleResource($article),
            ],
            __('awareness.fetched_successfully'),
            200
        );
    }
}
