<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorityTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'authority_type_id'
    ];

    public function authorityType()
    {
        return $this->belongsTo(AuthorityType::class);
    }
}
