<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwarenessArticle extends Model
{
    protected $fillable = [
        'icon_url',
        'news_type_id'
    ];

    public function newsType()
    {
        return $this->belongsTo(NewsType::class);
    }

    public function translations()
    {
        return $this->hasMany(AwarenessArticleTranslation::class);
    }

    public function currentTranslation()
    {
        return $this->hasOne(AwarenessArticleTranslation::class)->where('language_code', app()->getLocale());
    }
}
