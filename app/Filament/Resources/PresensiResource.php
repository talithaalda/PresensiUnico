<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Filament\Resources\PresensiResource\RelationManagers;
use App\Models\Presensi;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static ?string $pluralModelLabel = 'Presensi';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'pulang' => 'Pulang',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('created_at')->label('Waktu Check In')
                    ->required(),
                Forms\Components\DateTimePicker::make('checkout')->label('Waktu Check Out'),
                Forms\Components\TextInput::make('location')->label('Lokasi')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->searchable()
                    ->color(fn(string $state): string => match ($state) {
                        'pulang' => 'success',
                        'tidak hadir' => 'danger',
                        'hadir' => 'warning',
                        'sakit' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('checkin')
                    ->label('Check In')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('created_at', $direction);
                    })
                    ->getStateUsing(function ($record) {
                        return $record->created_at->format('H:i d/m/Y');
                    }),
                // Tanggal Check Out Column
                Tables\Columns\TextColumn::make('checkout')
                    ->label('Check Out')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('checkout', $direction);
                    })
                    ->getStateUsing(function ($record) {
                        if ($record->checkout) {
                            return $record->checkout->format('H:i d/m/Y');
                        }
                        return 'Belum Check Out';
                    }),
                Tables\Columns\TextColumn::make('selisih_waktu')
                    ->label('Lama Waktu')
                    ->getStateUsing(function ($record) {
                        if ($record->checkout) {
                            $checkinTime = $record->created_at;
                            $checkoutTime = $record->checkout;

                            $diffInSeconds = $checkinTime->diffInSeconds($checkoutTime);

                            $hours = floor($diffInSeconds / 3600);
                            $minutes = floor(($diffInSeconds % 3600) / 60);
                            $seconds = $diffInSeconds % 60;

                            // Tambahkan padding dengan str_pad
                            $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
                            $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
                            $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

                            return "{$hours}:{$minutes}:{$seconds}";
                        }
                        return 'Belum Check Out';
                    }),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->sortable()
                    ->limit(20)
                    ->searchable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make('table')->fromTable(),
                    ]),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'pulang' => 'Pulang',
                    ])
                    ->placeholder('Select Status'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Dari Tanggal Check In'),
                        DatePicker::make('created_until')->label('Sampai Tanggal Check In'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Filter::make('checkout')
                    ->form([
                        DatePicker::make('checkout_from')->label('Dari Tanggal Check Out'),
                        DatePicker::make('checkout_until')->label('Sampai Tanggal Check Out'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['checkout_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['checkout_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['checkout_from'] ?? null) {
                            $indicators['checkout_from'] = 'Created from ' . Carbon::parse($data['checkout_from'])->toFormattedDateString();
                        }
                        if ($data['checkout_until'] ?? null) {
                            $indicators['checkout_until'] = 'Created until ' . Carbon::parse($data['checkout_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
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
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}
