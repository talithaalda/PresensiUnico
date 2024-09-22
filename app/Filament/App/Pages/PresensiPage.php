<?php

namespace App\Filament\App\Pages;

use App\Models\Presensi;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class PresensiPage extends Page
{
    // protected static string $routePath = '/';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.presensi-page';
    protected static ?string $slug = '/';
    public $user;
    public $presensiId;
    public $presensi;
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public function getTitle(): string | Htmlable
    {
        return false;
    }

    public function mount()
    {
        $this->user = User::find(Auth::user()->id);
        $latestPresensi = $this->user->presensis()->latest()->first();
        if ($latestPresensi) {
            $this->presensiId = $latestPresensi->id;
            $this->presensi = $latestPresensi;
        } else {
            $this->presensiId = null;
            $this->presensi = null;
        }
    }
}
