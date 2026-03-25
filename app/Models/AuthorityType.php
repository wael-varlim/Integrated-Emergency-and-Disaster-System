<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorityType extends Model
{
    protected $fillable = [
        'type_name'
    ];


    public function authorityTranslation()
    {
        return $this->hasMany(AuthorityTranslation::class);
    }

    public function authority()
    {
        return $this->hasMany(Authority::class);
    }
}
