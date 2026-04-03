<?php
// app/Auth/AdminUserProvider.php

namespace App\Auth;

use App\Models\KnownUser;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Support\Str;

class AdminUserProvider extends EloquentUserProvider
{
    /**
     * Find the User by looking up email in known_users table.
     */
    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
             Str::contains(array_keys($credentials)[0], 'password'))) {
            return null;
        }

        $knownUser = KnownUser::where('email', $credentials['email'])->first();

        if (! $knownUser) {
            return null;
        }

        // Return the parent User model (which is Authenticatable)
        return $knownUser->user;
    }

    /**
     * Validate password against known_user's password.
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}