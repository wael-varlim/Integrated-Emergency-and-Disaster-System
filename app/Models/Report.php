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

    /**
     * Get the longitude from the geographic point.
     */
    protected function longitude(): Attribute
    {
        return Attribute::make(
            get: fn() => DB::selectOne(
                'SELECT ST_X(location) as longitude FROM reports WHERE id = ?',
                [$this->id]
            )?->longitude
        );
    }

    /**
     * Get the latitude from the geographic point.
     */
    protected function latitude(): Attribute
    {
        return Attribute::make(
            get: fn() => DB::selectOne(
                'SELECT ST_Y(location) as latitude FROM reports WHERE id = ?',
                [$this->id]
            )?->latitude
        );
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
