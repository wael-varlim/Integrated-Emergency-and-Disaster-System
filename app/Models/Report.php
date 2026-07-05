<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    protected $fillable = [
        'location',
        'news_id',
    ];

    protected $appends = ['coordinates'];

    /**
     * Override toArray to clean malformed UTF-8 characters
     */
    public function toArray(): array
    {
        $array = parent::toArray();
        return $this->sanitizeUtf8($array);
    }

    /**
     * Recursively sanitize UTF-8 in array
     */
    protected function sanitizeUtf8($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeUtf8'], $data);
        }
        
        if (is_string($data)) {
            // Remove invalid UTF-8 characters
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Get readable coordinates from geometry point
     */
    public function getCoordinatesAttribute(): ?string
    {
        if (!$this->location) {
            return null;
        }

        try {
            // Query to extract lat/lng from the POINT geometry
            $result = DB::selectOne(
                "SELECT ST_X(location) as longitude, ST_Y(location) as latitude FROM reports WHERE id = ?",
                [$this->id]
            );

            if ($result) {
                $lat = round($result->latitude, 6);
                $lng = round($result->longitude, 6);
                return "{$lat}, {$lng}";
            }
        } catch (\Exception $e) {
            // If extraction fails, return null
            return null;
        }

        return null;
    }

    /**
     * Get Google Maps link for the location
     */
    public function getGoogleMapsLinkAttribute(): ?string
    {
        if (!$this->coordinates) {
            return null;
        }

        [$lat, $lng] = explode(', ', $this->coordinates);
        return "https://www.google.com/maps?q={$lat},{$lng}";
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
