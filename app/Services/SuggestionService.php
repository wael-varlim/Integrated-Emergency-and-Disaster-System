<?php

namespace App\Services;

use App\Models\Suggestion;
use Illuminate\Database\Eloquent\Collection;

class SuggestionService
{
    /**
     * Create a new suggestion
     */
    public function createSuggestion(array $data): Suggestion
    {
        return Suggestion::create([
            'content' => $data['content'],
            'is_read_by_admin' => false,
        ]);
    }

    /**
     * Get all unread suggestions
     */
    public function getUnreadSuggestions(): Collection
    {
        return Suggestion::where('is_read_by_admin', false)
            ->latest()
            ->get();
    }

    /**
     * Mark suggestion as read
     */
    public function markAsRead(int $suggestionId): bool
    {
        $suggestion = Suggestion::findOrFail($suggestionId);
        return $suggestion->update(['is_read_by_admin' => true]);
    }

    /**
     * Get all suggestions
     */
    public function getAllSuggestions(): Collection
    {
        return Suggestion::latest()->get();
    }
}
