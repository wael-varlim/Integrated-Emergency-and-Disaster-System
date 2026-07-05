<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'media_url',
        'media_type_id',
        'model_type',
        'model_id',
    ];

    protected $appends = ['full_url'];

    /**
     * Get the full URL for the media file
     */
    public function getFullUrlAttribute(): string
    {
        $url = $this->media_url;
        
        // If URL already starts with http/https, return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // If URL starts with /storage/, it's already correct
        if (str_starts_with($url, '/storage/')) {
            return url($url);
        }
        
        // If URL starts with storage/, add leading slash
        if (str_starts_with($url, 'storage/')) {
            return url('/' . $url);
        }
        
        // Otherwise, assume it's in storage and prepend /storage/
        return url('/storage/' . ltrim($url, '/'));
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function mediaType()
    {
        return $this->belongsTo(MediaType::class);
    }
}
