<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard Indikator Mutu';

    protected static ?int $navigationSort = 1;

    public function getColumns(): array|int
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
            'xl' => 6,
            '2xl' => 6,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeWidget::class, // ✅ TAMBAHKAN INI DI PALING ATAS
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\UnitStatsWidget::class,
            \App\Filament\Widgets\SystemStatsWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
