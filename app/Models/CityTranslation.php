<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'city_id'
    ];


    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
