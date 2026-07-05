<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AwarenessArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('language_code', $locale);
        
        // Fallback to English if translation not found
        if (!$translation) {
            $translation = $this->translations->firstWhere('language_code', 'en');
        }
        
        return [
            'id' => $this->id,
            'title' => $translation?->title ?? '',
            'body' => $translation?->body ?? '',
            'icon_url' => $this->getFullIconUrl(),
            'news_type' => [
                'id' => $this->newsType->id,
                'name' => $this->newsType->type_name,
            ],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get full URL for icon
     */
    private function getFullIconUrl(): string
    {
        $url = $this->icon_url;
        
        // If URL already starts with http/https, return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // If URL starts with /storage/, it's already correct
        if (str_starts_with($url, '/storage/')) {
            return url($url);
        }
        
        // If URL starts with storage/, add leading slash
        if (str_starts_with($url, 'storage/')) {
            return url('/' . $url);
        }
        
        // Otherwise, assume it's in storage and prepend /storage/
        return url('/storage/' . ltrim($url, '/'));
    }
}
