<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'region_id'
    ];



    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
