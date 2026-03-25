<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwarenessArticle extends Model
{
    protected $fillable = [
        'title',
        'body',
        'icon_url',
        'news_type_id'
    ];


    public function newsType()
    {
        return $this->belongsTo(NewsType::class);
    }
}
