<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'media_type_id'
    ];



    public function mediaType()
    {
        return $this->belongsTo(MediaType::class);
    }
}
