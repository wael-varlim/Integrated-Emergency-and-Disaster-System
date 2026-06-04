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
        'created_at' => $this->created_at,

        'location' => [
            'longitude' => $this->news?->report?->longitude,
            'latitude'  => $this->news?->report?->latitude,
        ],

        'address' => [
            'street'      => $this->news?->address?->street,
            'city'        => $this->news?->address?->city?->name,
            'governorate' => $this->news?->address?->city?->governorate?->name,
        ],
        'types' => $this->news?->newsType?->pluck('type_name'),
        'media' => $this->news?->media?->first()?->media_url,
    ];
    }
}
