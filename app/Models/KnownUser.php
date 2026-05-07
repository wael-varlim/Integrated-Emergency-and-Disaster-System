<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnownUser extends Model
{

    protected $hidden = ['password'];

    protected $fillable = [
        'user_id',
        'official_identifier',
        'official_identifier_method',
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
