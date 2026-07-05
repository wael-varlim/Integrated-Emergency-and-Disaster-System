<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwarenessArticleTranslation extends Model
{
    protected $fillable = [
        'language_code',
        'title',
        'body',
        'awareness_article_id'
    ];

    public function awarenessArticle()
    {
        return $this->belongsTo(AwarenessArticle::class);
    }
}
