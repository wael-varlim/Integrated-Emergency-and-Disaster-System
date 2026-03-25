<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = [
        'name',
        'region_id'
    ];



    public function governorateTranslation()
    {
        return $this->hasMany(GovernorateTranslation::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->hasMany(City::class);
    }
}
