<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernorateTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'governorate_id'
    ];



    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
