<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'owner_role',
        'news_id',
    ];



    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }
}
