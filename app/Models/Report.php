<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    protected $fillable = [
        'location',
        'news_id',
    ];



    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
