<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BasePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function sharedFields(): array
    {
        return [
            'created_at' => [
                'date' => $this->created_at?->format('j, F, Y'),
                'time' => $this->created_at?->format('g:i a'),
            ],

            'address' => [
                'street'      => $this->news?->address?->currentTranslation?->translation??
                    $this->news?->address?->street,
                'city'        => $this->news?->address?->city?->currentTranslation?->translation,
                'governorate' => $this->news?->address?->city?->governorate?->currentTranslation?->translation,
            ],

            'types' => $this->news?->newsType?->map(function ($type) {
                return $type->currentTranslation?->translation;
            }),

            'media' => $this->getMediaUrl(),
        ];
    }

    /**
     * Get properly formatted media URL
     */
    protected function getMediaUrl(): ?string
    {
        $url = $this->news?->media?->first()?->media_url;
        
        if (!$url) {
            return null;
        }
        
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        return asset('storage/' . $url);
    }

    // public function toArray(Request $request): array
    // {
    // }
}
