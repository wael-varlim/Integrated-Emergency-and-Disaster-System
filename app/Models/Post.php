<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'owner_role',
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


    public function postTranslation()
    {
        return $this->hasMany(PostTranslation::class);
    }

    //current trnaslation relation
    public function currentTranslation()
    {
        return $this->hasOne(PostTranslation::class)->where('language_code', app()->getLocale());
    }
}
