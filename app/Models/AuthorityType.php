<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorityType extends Model
{
    protected $fillable = [
        'type_name'
    ];



    public function authority()
    {
        return $this->hasMany(Authority::class);
    }

    public function newsType()
    {
        return $this->belongsToMany(NewsType::class, 'authority_types_news_types');
    }
}
