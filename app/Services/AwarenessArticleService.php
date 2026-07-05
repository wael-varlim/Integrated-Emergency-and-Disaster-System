<?php

namespace App\Services;

use App\Models\AwarenessArticle;
use Illuminate\Database\Eloquent\Collection;

class AwarenessArticleService
{
    /**
     * Get all awareness articles with their news types
     */
    public function getAllArticles(): Collection
    {
        return AwarenessArticle::with(['newsType', 'translations'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get articles by news type
     */
    public function getArticlesByType(int $newsTypeId): Collection
    {
        return AwarenessArticle::with(['newsType', 'translations'])
            ->where('news_type_id', $newsTypeId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a specific article by ID
     */
    public function getArticleById(int $id): ?AwarenessArticle
    {
        return AwarenessArticle::with(['newsType', 'translations'])
            ->findOrFail($id);
    }

    /**
     * Get latest articles (limit)
     */
    public function getLatestArticles(int $limit = 10): Collection
    {
        return AwarenessArticle::with(['newsType', 'translations'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
