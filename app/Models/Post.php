<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'news_id',
        'by_admin'
    ];



    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    public function postTranslations()
    {
        return $this->hasMany(PostTranslation::class);
    }

    //current translation relation
    public function currentTranslation()
    {
        return $this->hasOne(PostTranslation::class)->where('language_code', app()->getLocale());
    }
}
