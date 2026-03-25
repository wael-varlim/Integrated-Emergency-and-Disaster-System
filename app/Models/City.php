<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'governorate_id',
        'region_id'
    ];



    public function cityTranslation()
    {
        return $this->hasMany(CityTranslation::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
