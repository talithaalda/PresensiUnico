<?php

namespace App\Filament\App\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Psy\Readline\Hoa\Console;
use Throwable;

class EditProfile extends BaseEditProfile
{


    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }


    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('current_password')
            ->label(__('Current Password'))
            ->password()
            ->dehydrated(false)
            ->autocomplete(false)
            ->revealable(filament()->arePasswordsRevealable())
            ->rules(['current_password:web'])
            ->required(function (callable $get) {
                // Make current password required if new password is filled
                return !empty($get('password'));
            });
    }
    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->rules([
                Password::min(6)
                    ->letters()
                    ->numbers()
            ])
            ->autocomplete('new-password')
            ->dehydrateStateUsing(fn($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'))
            ->required(function (callable $get) {
                // Make current password required if new password is filled
                return !empty($get('current_password'));
            });
    }
    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->dehydrated(false);
    }
}
