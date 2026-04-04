<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnownUser extends Model
{


    protected $fillable = [
        'user_id',
        'national_number',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
