<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseTrait;
use App\Http\Requests\Suggestion\StoreSuggestionRequest;
use App\Models\Suggestion;
use App\Services\SuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected SuggestionService $suggestionService
    ) {}

    /**
     * Store a newly created suggestion in storage.
     */
    public function store(StoreSuggestionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $suggestion = $this->suggestionService->createSuggestion($data);

        return $this->apiResponse(
            [
                'suggestion' => $suggestion,
            ],
            __('suggestion.created_successfully'),
            201
        );
    }

    /**
     * Display a listing of all suggestions.
     */
    public function index(): JsonResponse
    {
        $suggestions = $this->suggestionService->getAllSuggestions();

        return $this->apiResponse(
            [
                'suggestions' => $suggestions,
            ],
            __('suggestion.fetched_successfully'),
            200
        );
    }

    /**
     * Display the specified suggestion.
     */
    public function show(Suggestion $suggestion): JsonResponse
    {
        return $this->apiResponse(
            [
                'suggestion' => $suggestion,
            ],
            __('suggestion.fetched_successfully'),
            200
        );
    }

    /**
     * Mark suggestion as read by admin.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $this->suggestionService->markAsRead($id);

        return $this->apiResponse(
            null,
            __('suggestion.marked_as_read'),
            200
        );
    }
}
