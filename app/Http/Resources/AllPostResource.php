<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AllPostResource extends BasePostResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge([
            'id' => $this->id,
            'by_admin' => $this->by_admin,
            'title' => $this->currentTranslation?->translation,
            'body' => $this->news?->currentTranslation?->translation,
            'location' => $this->news?->report ? [
                'longitude' => $this->news->report->longitude,
                'latitude'  => $this->news->report->latitude,
            ] : null,
        ], $this->sharedFields());
    }
}
