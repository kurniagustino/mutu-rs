<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicatorResult;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $totalDataEntry = HospitalSurveyIndicatorResult::count();

        return [
            Stat::make('Total Data Entry Semua Unit', number_format($totalDataEntry))
                ->description('Akumulasi data entry sistem')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('gray')
                ->chart([70000, 72000, 74000, 76000, 77000, 77500, 77760]),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }
}
