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

    public function newsTypeTranslation()
    {
        return $this->hasMany(NewsTypeTranslation::class);
    }

    public function arabicTranslation()
    {
        return $this->hasOne(NewsTypeTranslation::class)
            ->where('language_code', 'ar');
    }

    public function awarenessArticle()
    {
        return $this->hasOne(AwarenessArticle::class);
    }

    public function authorityType()
    {
        return $this->belongsToMany(AuthorityType::class, 'authority_types_news_types');
    }

    //current trnaslation relation
    public function currentTranslation()
    {
        return $this->hasOne(NewsTypeTranslation::class)->where('language_code', app()->getLocale());
    }
}
