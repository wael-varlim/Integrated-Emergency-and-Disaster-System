<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'posts' => PostResource::collection($this->collection),
        ];
    }

    public function paginationInformation($request, $paginated, $default)
    {
        if ($default['meta']['total'] === 0) {
            return [];
        }

        return [
            'pagination' => [
                'navigation' => [
                    'first_page' => $default['links']['first'],
                    'last_page' => $default['links']['last'],
                    'previous_page' => $default['links']['prev'],
                    'next_page' => $default['links']['next'],
                ],

                'info' => [
                    'current_page' => $default['meta']['current_page'],
                    'from' => $default['meta']['from'],
                    'to' => $default['meta']['to'],
                    'last_page' => $default['meta']['last_page'],
                    'per_page' => $default['meta']['per_page'],
                    'total' => $default['meta']['total'],
                ],
            ],
        ];
    }
}
