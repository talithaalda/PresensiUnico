<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\EmailVerificationPrompt;
use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\Auth\RequestPasswordReset;
use App\Filament\App\Pages\EditProfile;
use App\Filament\App\Pages\PresensiPage;
use App\Models\User;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Notifications\Notification;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use PharIo\Manifest\Email;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('/')
            ->path('/')
            ->colors([
                'primary' => Color::hex('#38BDF8'),
            ])
            ->defaultThemeMode(ThemeMode::Dark)
            ->favicon(asset('images/logo.png'))
            ->brandLogo(asset('images/logo-name.png'))
            ->brandLogoHeight('3rem')
            ->darkModeBrandLogo(asset('images/logo-name-dark.png'))
            ->login()
            ->registration(Register::class)
            ->passwordReset(RequestPasswordReset::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->profile(EditProfile::class)
            ->navigation(false)
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/admin')
                    ->visible(fn(): bool => Auth::user()->is_admin),

                MenuItem::make()
                    ->label('Delete Account')
                    ->icon('heroicon-o-trash')
                    ->url('/delete-account')
                    ->color('danger')
                    ->visible(fn(): bool => Auth::user()->is_admin === false),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                PresensiPage::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->topNavigation();
    }
}
