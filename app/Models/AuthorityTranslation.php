<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorityTranslation extends Model
{
    protected $fillable = [
        'language_code',
        'translation',
        'authority_id'
    ];

    public function authority()
    {
        return $this->belongsTo(Authority::class);
    }
}
