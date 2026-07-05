<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'body',
        'user_id',
        'known_user_id',
        'address_id'
    ];

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

    public function knownUser() 
    {
        return $this->belongsTo(KnownUser::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function authority()
    {
        return $this->belongsToMany(Authority::class, 'authorities_news');
    }

    public function newsType()
    {
        return $this->belongsToMany(NewsType::class, 'news_types_news');
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }


    public function newsTranslations()
    {
        return $this->hasMany(NewsTranslation::class);
    }

    //current translation relation
    public function currentTranslation()
    {
        return $this->hasOne(NewsTranslation::class)->where('language_code', app()->getLocale());
    }
}
