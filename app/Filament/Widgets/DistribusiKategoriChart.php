<?php

namespace App\Filament\Widgets;

use App\Models\ImutCategory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class DistribusiKategoriChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Kategori IMUT';

    protected function getData(): array
    {
        $user = Auth::user();
    $idUnit = $user->ruanganUtama->id_unit ?? null;
        $year = now()->year;

        if (!$idUnit) {
            return [
                'datasets' => [[
                    'data' => [],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                    ],
                ]],
                'labels' => [],
            ];
        }

        try {
            $categories = ImutCategory::withCount(['indicators as total_results' => function ($q) use ($idUnit, $year) {
                $q->whereHas('results', function ($q2) use ($year) {
                    $q2->whereYear('result_post_date', $year);
                })
                    ->whereHas('units', function ($q3) use ($idUnit) {
                        $q3->where('unit.id', $idUnit);
                    });
            }])
                ->having('total_results', '>', 0)
                ->get();

            if ($categories->isEmpty()) {
                return [
                    'datasets' => [[
                        'data' => [],
                        'backgroundColor' => [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#ec4899',
                        ],
                    ]],
                    'labels' => [],
                ];
            }

            return [
                'datasets' => [
                    [
                        'data' => $categories->pluck('total_results')->toArray(),
                        'backgroundColor' => [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#ec4899',
                        ],
                    ],
                ],
                'labels' => $categories->pluck('imut')->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'datasets' => [[
                    'data' => [],
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                    ],
                ]],
                'labels' => [],
            ];
        }
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getDescription(): ?string
    {
        return 'Distribusi pendataan berdasarkan kategori IMUT';
    }
}
