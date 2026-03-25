<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsType extends Model
{
    protected $fillable = [
        'type_name'
    ];


    public function news()
    {
        return $this->belongsToMany(News::class);
    }

    public function newsTranslation()
    {
        return $this->hasMany(NewsTranslation::class);
    }

    public function awarenessArticle()
    {
        return $this->hasOne(AwarenessArticle::class);
    }
}
