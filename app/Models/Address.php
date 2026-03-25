<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street',
        'city_id'
    ];



    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function addressTranslation()
    {
        return $this->hasMany(AddressTranslation::class);
    }
}
