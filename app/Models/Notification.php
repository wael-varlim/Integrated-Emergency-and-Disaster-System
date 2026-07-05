<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'region_id',
        'post_id',
    ];



    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function notificationTranslations()
    {
        return $this->hasMany(NotificationTranslations::class);
    }

    public function currentTranslation()
    {
        return $this->hasOne(NotificationTranslations::class)
            ->where('language_code', app()->getLocale());
    }
}
