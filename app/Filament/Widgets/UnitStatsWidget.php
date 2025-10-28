<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UnitStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = Auth::user();
        $unitId = $user->id_ruang;
        $unitName = $user->departemen->nama_ruang ?? 'Unit Anda';

        // Indikator unit ini
        $unitIndicators = HospitalSurveyIndicator::whereHas('departemens', function ($query) use ($unitId) {
            $query->where('id_ruang', $unitId);
        })->count();

        // Indikator unit yang sudah diinput bulan ini
        $unitCompletedThisMonth = HospitalSurveyIndicatorResult::where('result_department_id', $unitId)
            ->whereYear('result_period', Carbon::now()->year)
            ->whereMonth('result_period', Carbon::now()->month)
            ->distinct('result_indicator_id')
            ->count('result_indicator_id');

        // Total data entry unit (all time)
        $totalUnitDataEntry = HospitalSurveyIndicatorResult::where('result_department_id', $unitId)->count();

        // Progress percentage
        $progressPercentage = $unitIndicators > 0
            ? round(($unitCompletedThisMonth / $unitIndicators) * 100, 1)
            : 0;

        return [
            Stat::make('Jumlah Indikator Unit '.$unitName, number_format($unitIndicators))
                ->description('Total indikator di unit Anda')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success')
                ->chart([1, 2, 2, 3, 3, 3, 3]),

            Stat::make('Progress Input Bulan Ini', $unitCompletedThisMonth.' / '.$unitIndicators)
                ->description("Progress: {$progressPercentage}% (".Carbon::now()->format('F Y').')')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($progressPercentage >= 80 ? 'success' : ($progressPercentage >= 50 ? 'warning' : 'danger'))
                ->chart([0, 10, 20, 30, 40, 50, $unitCompletedThisMonth]),

            Stat::make('Total Data Entry Unit', number_format($totalUnitDataEntry))
                ->description('Akumulasi data entry unit')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([50, 55, 60, 65, 68, 69, 69]),
        ];
    }
}
