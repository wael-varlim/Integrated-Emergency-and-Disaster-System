<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'location',
        'news_id'
    ];



    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
