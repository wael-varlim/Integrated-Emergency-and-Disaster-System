<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressTranslation extends Model
{
    protected $fillable = [
        'languahe_code',
        'translation',
        'address_id'
    ];



    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
