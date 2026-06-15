<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'post_id'
    ];



    public function postType()
    {
        return $this->belongsTo(NewsType::class);
    }
}
