<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class NormalPostResource extends BasePostResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge($this->sharedFields(), [
            'location' => [
                'longitude' => $this->news?->report?->longitude,
                'latitude'  => $this->news?->report?->latitude,
            ],
        ]);
    }
}
