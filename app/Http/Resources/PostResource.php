<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
    return [
        'title' => $this->title,
        'created_at' => [
            'date' => $this->created_at?->format('j, F, Y'),
            'time' => $this->created_at?->format('g:i a'),
        ],

        'location' => [
            'longitude' => $this->news?->report?->longitude,
            'latitude'  => $this->news?->report?->latitude,
        ],

        'address' => [
            'street'      => $this->news?->address?->currentTranslation?->translation,
            'city'        => $this->news?->address?->city?->currentTranslation?->translation,
            'governorate' => $this->news?->address?->city?->governorate?->currentTranslation?->translation,
        ],
        'types' => $this->news?->newsType?->map(function ($type) {
            return $type->currentTranslation?->translation;
        }),
        'media' => $this->news?->media?->first()?->media_url,
    ];
    }
}
