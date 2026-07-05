<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('/')
            ->login()
            ->authGuard('admin')
            ->colors([
                'primary' => [
                    50  => '#e6f2f1',
                    100 => '#cce5e4',
                    200 => '#99cbc8',
                    300 => '#66b1ad',
                    400 => '#339791',
                    500 => '#002623', // Main color
                    600 => '#001e1c',
                    700 => '#001715',
                    800 => '#000f0e',
                    900 => '#000807',
                    950 => '#000403',
                ],
                'secondary' => [
                    50  => '#faf8f5',
                    100 => '#f5f1eb',
                    200 => '#ebe3d7',
                    300 => '#e1d5c3',
                    400 => '#d7c7af',
                    500 => '#b9a77a', // Gold/tan accent
                    600 => '#a18d5e',
                    700 => '#897346',
                    800 => '#715931',
                    900 => '#593f1f',
                    950 => '#412510',
                ],
                'danger'  => Color::Rose,
                'info'    => [
                    50  => '#e6f2f1',
                    100 => '#cce5e4',
                    200 => '#99cbc8',
                    300 => '#66b1ad',
                    400 => '#339791',
                    500 => '#007d76',
                    600 => '#006460',
                    700 => '#004b4a',
                    800 => '#003234',
                    900 => '#00191e',
                ],
                'success' => Color::Emerald,
                'warning' => [
                    50  => '#faf8f5',
                    100 => '#f5f1eb',
                    200 => '#ebe3d7',
                    300 => '#e1d5c3',
                    400 => '#d7c7af',
                    500 => '#b9a77a',
                    600 => '#a18d5e',
                    700 => '#897346',
                    800 => '#715931',
                    900 => '#593f1f',
                ],
            ])
            ->brandName('Emergency & Disaster System')
            ->globalSearch(false)
            ->font('Inter')
            ->renderHook(
                'panels::styles.after',
                fn () => '<link rel="stylesheet" href="' . asset('css/dashboard-opacity.css') . '">'
            )
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
