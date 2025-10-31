<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicatorResult;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TrendCapaianMutuChart extends ChartWidget
{
    protected ?string $heading = 'Trend Capaian Mutu Bulanan';

    public ?string $filter = 'year';

    protected function getData(): array
    {
        $user = Auth::user();
        $idUnit = $user->ruangan_utama->id_unit ?? null; // 👈 LOGIKA BARU
        $year = now()->year;

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $count = HospitalSurveyIndicatorResult::whereYear('result_post_date', $year)
                ->whereMonth('result_post_date', $month)
                ->whereHas('indicator.units', function ($q) use ($idUnit) { // 👈 PERBAIKAN
                    $q->where('unit.id', $idUnit); // 👈 PERBAIKAN
                })
                ->count();

            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendataan',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'year' => 'Tahun Ini',
            'last_year' => 'Tahun Lalu',
        ];
    }

    public function getDescription(): ?string
    {
        return 'Deskripsi chart';
    }
}
