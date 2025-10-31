<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicator;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TopIndikatorChart extends ChartWidget
{
    protected ?string $heading = 'Top 5 Indikator Mutu';

    protected function getData(): array
    {
        $user = Auth::user();
        $idUnit = $user->ruangan_utama->id_unit ?? null; // 👈 LOGIKA BARU
        $year = now()->year;

        $indicators = HospitalSurveyIndicator::whereHas('units', function ($q) use ($idUnit) { // 👈 PERBAIKAN
            $q->where('unit.id', $idUnit); // 👈 PERBAIKAN
        })
            ->withCount(['results' => function ($q) use ($year) {
                $q->whereYear('result_post_date', $year);
            }])
            ->orderBy('results_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendataan',
                    'data' => $indicators->pluck('results_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b',
                        '#10b981',
                    ],
                ],
            ],
            'labels' => $indicators->map(fn ($ind) => strlen($ind->indicator_title) > 30
                    ? substr($ind->indicator_title, 0, 30).'...'
                    : $ind->indicator_title
            )->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    public function getDescription(): ?string
    {
        return 'Deskripsi chart';
    }
}
