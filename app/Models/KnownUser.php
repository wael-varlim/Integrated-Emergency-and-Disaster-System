<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class KnownUser extends Model
{
    use HasApiTokens;


    protected $fillable = [
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
