<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    protected $fillable = [
        'language_code',
        'translation',
        'post_id'
    ];



    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
