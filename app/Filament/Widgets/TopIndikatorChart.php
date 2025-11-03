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
        $idUnit = $user->ruangan_utama->id_unit ?? null;
        $year = now()->year;

        if (!$idUnit) {
            return [
                'datasets' => [[
                    'label' => 'Total Pendataan',
                    'data' => [],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b',
                        '#10b981',
                    ],
                ]],
                'labels' => [],
            ];
        }

        try {
            $indicators = HospitalSurveyIndicator::whereHas('units', function ($q) use ($idUnit) {
                $q->where('unit.id', $idUnit);
            })
                ->withCount(['results' => function ($q) use ($year) {
                    $q->whereYear('result_post_date', $year);
                }])
                ->orderBy('results_count', 'desc')
                ->limit(5)
                ->get();

            if ($indicators->isEmpty()) {
                return [
                    'datasets' => [[
                        'label' => 'Total Pendataan',
                        'data' => [],
                        'backgroundColor' => [
                            '#3b82f6',
                            '#8b5cf6',
                            '#ec4899',
                            '#f59e0b',
                            '#10b981',
                        ],
                    ]],
                    'labels' => [],
                ];
            }

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
                'labels' => $indicators->map(fn ($ind) => strlen($ind->indicator_name ?? '') > 30
                        ? substr($ind->indicator_name, 0, 30).'...'
                        : ($ind->indicator_name ?? 'N/A')
                )->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'datasets' => [[
                    'label' => 'Total Pendataan',
                    'data' => [],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b',
                        '#10b981',
                    ],
                ]],
                'labels' => [],
            ];
        }
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
        return 'Top 5 indikator mutu dengan pendataan terbanyak';
    }
}
