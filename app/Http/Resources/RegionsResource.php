<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'preferred_language' => $this->preferred_language,
            'regions' => $this->whenLoaded('regions', fn () =>
                $this->regions->map(fn ($region) => $region->city?->name ?? $region->governorate?->name)
            ),
        ];    
    }
}
