<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{
    protected $fillable = [
        'type_name'
    ];



    public function media()
    {
        return $this->hasMany(Media::class);
    }


}
