<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'news_type_id'
    ];



    public function newsType()
    {
        return $this->belongsTo(NewsType::class);
    }
}
