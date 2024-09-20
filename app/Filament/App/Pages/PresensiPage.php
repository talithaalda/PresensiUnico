<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class PresensiPage extends Page
{
    // protected static string $routePath = '/';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.presensi-page';
    protected static ?string $slug = '/';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public function getTitle(): string | Htmlable
    {
        return false;
    }
}
