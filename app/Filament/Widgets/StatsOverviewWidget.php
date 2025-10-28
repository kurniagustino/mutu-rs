<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = today();
        $currentMonth = Carbon::now()->format('Y-m');

        // Total indikator
        $totalIndicators = HospitalSurveyIndicator::count();

        // ✅ Indikator yang sudah diinput bulan ini (berdasarkan result_period)
        $completedThisMonth = HospitalSurveyIndicatorResult::whereYear('result_period', Carbon::now()->year)
            ->whereMonth('result_period', Carbon::now()->month)
            ->distinct('result_indicator_id')
            ->count('result_indicator_id');

        // ✅ Indikator pending bulan ini
        $pendingThisMonth = max(0, $totalIndicators - $completedThisMonth);

        // ✅ Total data entry bulan ini (berdasarkan created_at)
        $dataEntryThisMonth = HospitalSurveyIndicatorResult::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return [
            Stat::make('Total Indikator', number_format($totalIndicators))
                ->description('Semua indikator di sistem')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info')
                ->chart([130, 132, 133, 134, 135, 135, 135]),

            Stat::make('Indikator Pending Bulan Ini', number_format($pendingThisMonth))
                ->description('Belum diinput periode '.Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([50, 45, 40, 35, 30, 25, 20]),

            Stat::make('Unit Input Bulan Ini', number_format($completedThisMonth))
                ->description('Sudah input periode '.Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([0, 10, 20, 30, 40, 50, 69]),
        ];
    }
}
