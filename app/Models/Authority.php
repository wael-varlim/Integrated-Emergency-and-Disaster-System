<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    protected $fillable = [
        'authority_type_id'
    ];


    public function authorityType()
    {
        return $this->belongsTo(AuthorityType::class);
    }

    public function news()
    {
        return $this->belongsToMany(News::class);
    }
}
