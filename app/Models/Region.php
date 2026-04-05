<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    
    protected $appends = ['name'];

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function governorate()
    {
        return $this->hasOne(Governorate::class);
    }

    public function city()
    {
        return $this->hasOne(City::class);
    }

    public function getNameAttribute()
    {
        return $this->city->name;
    }

}
