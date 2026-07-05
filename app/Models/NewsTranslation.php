<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    protected $fillable = [
        'language_code',
        'translation',
        'news_id'
    ];



    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
