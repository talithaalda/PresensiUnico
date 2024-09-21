<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->unique(table: User::class, column: 'email', ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('position'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->autocomplete('new-password')
                    ->revealable()
                    // ->hiddenOn('edit')
                    ->minLength(6)
                    ->rules([
                        Password::min(6)
                            ->letters()
                            ->numbers()
                    ]),

                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->label('Confirm Password')
                    ->revealable()
                    // ->hiddenOn('edit')
                    ->same('password')
                    ->nullable(),
                Forms\Components\Toggle::make('is_admin'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_admin')->formatStateUsing(fn($state) => $state ? 'Admin' : 'Karyawan')
                    ->sortable()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_admin')
                    ->options([
                        '0' => 'Karyawan',
                        '1' => 'Admin',
                    ])
                    ->placeholder('Is Admin'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),

        ];
    }
}
