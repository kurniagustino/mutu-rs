<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicatorResult;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class StatusCapaianChart extends ChartWidget
{
    protected ?string $heading = 'Status Capaian Target';

    protected function getData(): array
    {
        $user = Auth::user();
        $idUnit = $user->ruangan_utama->id_unit ?? null; // 👈 LOGIKA BARU
        $year = now()->year;

        $total = HospitalSurveyIndicatorResult::whereYear('result_post_date', $year)
            ->whereHas('indicator.units', function ($q) use ($idUnit) { // 👈 PERBAIKAN
                $q->where('unit.id', $idUnit); // 👈 PERBAIKAN
            })
            ->count();

        // Simulasi: 75% tercapai, 25% belum
        $achieved = round($total * 0.75);
        $notAchieved = $total - $achieved;

        return [
            'datasets' => [
                [
                    'data' => [$achieved, $notAchieved],
                    'backgroundColor' => [
                        '#10b981',
                        '#ef4444',
                    ],
                ],
            ],
            'labels' => ['Tercapai Target', 'Belum Tercapai'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getDescription(): ?string
    {
        return 'Deskripsi chart';
    }
}
