<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'body',
        'user_id'
    ];


    public function user() 
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function authority()
    {
        return $this->belongsToMany(Authority::class, 'authorities_news');
    }

    public function newsType()
    {
        return $this->belongsToMany(NewsType::class, 'news_types_news');
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }
}
