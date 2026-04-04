<?php
// app/Models/User.php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;


    // tells Spatie to check permissions on 'web' guard
    protected $guard_name = 'web';

    protected $fillable = [
        'user_type',
    ];

    protected $hidden = [
        'remember_token',
    ];


    /**
     * Filament uses this for the login form & password validation.
     */
    public function getAuthPassword(): ?string
    {
        return $this->knownUser?->password;
    }

    /**
     * Filament displays the user's name in the top-right corner.
     */
    public function getNameAttribute(): string
    {
        return $this->knownUser
            ? $this->knownUser->first_name . ' ' . $this->knownUser->last_name
            : 'Unknown';
    }

    /**
     * Filament needs email for avatar, display, etc.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->knownUser?->email;
    }

    /**
     * Used by Filament to generate Gravatar / avatar fallback.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        return null;
    }

    // =========================================
    // Filament Access Control
    // =========================================

    public function canAccessPanel(Panel $panel): bool
    {

        if ($panel->getId() === 'admin') {
            //  Allow anyone with ANY role (all sub-admins have at least one role)
            return $this->roles()->where('guard_name', 'web')->exists();
        }

        return false;
    }

    // =========================================
    // Relationships 
    // =========================================

    public function knownUser()
    {
        return $this->hasOne(KnownUser::class);
    }

    public function anonymousUser()
    {
        return $this->hasOne(AnonymousUser::class);
    }

    public function fcmToken()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function region()
    {
        return $this->belongsToMany(Region::class);
    }
}