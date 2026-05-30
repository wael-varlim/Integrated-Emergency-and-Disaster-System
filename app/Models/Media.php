<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'media_url',
        'media_type_id',
        'model_type',
        'model_id',
    ];




    public function model()
    {
        return $this->morphTo();
    }

    public function mediaType()
    {
        return $this->belongsTo(MediaType::class);
    }
}
