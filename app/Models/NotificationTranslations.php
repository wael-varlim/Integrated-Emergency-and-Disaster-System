<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTranslations extends Model
{
    protected $fillable = [
        'language_code',
        'title_translation',
        'body_translation',
        'notification_id'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
