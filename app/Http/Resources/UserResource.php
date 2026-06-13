<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'official_identifier'        => $this->official_identifier,
            'official_identifier_method' => $this->official_identifier_method,
            'first_name'                 => $this->first_name,
            'last_name'                  => $this->last_name,
            'email'                      => $this->email,
        ];
    }
}
