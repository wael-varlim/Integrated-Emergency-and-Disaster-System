<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "created_at" => [
                "date" => $this->created_at?->format("j, F, Y"),
                "time" => $this->created_at?->format("g:i a"),
            ],
            "location" => [
                "longitude" => $this->news?->report?->longitude,
                "latitude" => $this->news?->report?->latitude,
            ],
            "address" => [
                "street" =>
                    $this->news?->address?->currentTranslation?->translation ??
                    $this->news?->address?->street,
                "city" =>
                    $this->news?->address?->city?->currentTranslation
                        ?->translation,
                "governorate" =>
                    $this->news?->address?->city?->governorate
                        ?->currentTranslation?->translation,
            ],
            "news_type" => $this->news?->newsType?->first()?->currentTranslation
                ?->translation,
            "body" => $this->news?->body,
            "media" => $this->news?->media?->first()?->media_url,
        ];
    }
}
