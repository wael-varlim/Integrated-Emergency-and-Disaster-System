<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
//use App\Http\Resources\BasePostResource;

class AdminPostResource extends BasePostResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge([
            'title' => $this->currentTranslation?->translation,
            'body' => $this->news?->currentTranslation?->translation,

        ], $this->sharedFields());
    }
}
