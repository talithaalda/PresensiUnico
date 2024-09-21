<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Total users registered')
                ->color('danger'), // Apply red color for total users

            Stat::make('Today\'s Presences', Presensi::query()->whereDate('created_at', now()->toDateString())->count())
                ->description('Total presences recorded today')
                ->color('success'), // Apply green color for today’s presence count

            Stat::make('This Week\'s Presences', Presensi::query()->where('created_at', '>=', now()->startOfWeek())->count())
                ->description('Total presences recorded this week')
                ->color('warning'), // Apply yellow color for this week’s presence count

            Stat::make('This Year\'s Presences', Presensi::query()->where('created_at', '>=', now()->startOfYear())->count())
                ->description('Total presences recorded this year')
                ->color('primary'), // Apply blue color for this year’s presence count

        ];
    }
}
