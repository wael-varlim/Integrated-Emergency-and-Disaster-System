<?php

namespace App\Services;

use App\Models\Authority;
use App\Models\AuthorityType;
use Illuminate\Support\Collection;

class AuthorityService
{
    public function FindMatchingAuthorityTypes(array $newsTypeIds): Collection
    {
        return AuthorityType::whereHas('newsType', function ($query) use ($newsTypeIds) {
            $query->whereIn('news_types.id', $newsTypeIds);
        })->get();
    }

    public function findNearestAuthorities(?Collection $authorityTypes, float $latitude, float $longitude)
    {
        if($authorityTypes == null)
        {
            return null;
        }
        
        $nearestAuthorities = collect();
        $point = "POINT({$latitude} {$longitude})";

        foreach ($authorityTypes as $authorityType) {
            $nearest = Authority::where('authority_type_id', $authorityType->id)
                ->selectRaw(
                    '*, ST_Distance_Sphere(location, ST_GeomFromText(?, 4326)) as distance',
                    [$point]
                )
                ->orderBy('distance')
                ->first();

            if ($nearest) {
                $nearestAuthorities->push($nearest);
            }
        }

        return $nearestAuthorities;
    }

}