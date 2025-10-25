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
        $idRuang = $user->id_ruang;
        $year = now()->year;

        $categories = ImutCategory::withCount(['indicators as total_results' => function ($q) use ($idRuang, $year) {
            $q->whereHas('results', function ($q2) use ($year) {
                $q2->whereYear('result_post_date', $year);
            })
                ->whereHas('departemens', function ($q3) use ($idRuang) {
                    $q3->where('id_ruang', $idRuang);
                });
        }])
            ->having('total_results', '>', 0)
            ->get();

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
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public function getDescription(): ?string
    {
        return 'Deskripsi chart';
    }
}
