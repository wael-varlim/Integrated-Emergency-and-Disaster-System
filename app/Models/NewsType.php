<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsType extends Model
{
    protected $hidden = [
        'pivot'
    ];
    
    protected $fillable = [
        'type_name'
    ];


    public function news()
    {
        return $this->belongsToMany(News::class, 'news_types_news');
    }

    public function newsTranslation()
    {
        return $this->hasMany(NewsTranslation::class);
    }

    public function awarenessArticle()
    {
        return $this->hasOne(AwarenessArticle::class);
    }


    //current trnaslation relation
    public function currentTranslation()
    {
        return $this->hasOne(NewsTranslation::class)->where('language_code', app()->getLocale());
    }
}
