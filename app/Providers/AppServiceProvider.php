<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Auth\AdminUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ✅ Move to register() instead of boot()
    public function register(): void
    {
        Auth::provider('admin-provider', function ($app, array $config) {
            return new AdminUserProvider(
                $app['hash'],
                $config['model'],
            );
        });
    }

    public function boot(): void
    {
        //
    }
}