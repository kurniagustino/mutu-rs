<?php

namespace App\Filament\Widgets;

use App\Models\HospitalSurveyIndicatorResult;
use App\Models\HospitalSurveyIndicator;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GrafikMutuChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Mutu';
    public ?int $indikator = null;
    public ?int $tahun = null;

    protected function getData(): array
    {
        $user = Auth::user();
        $ruanganId = $user?->ruanganUtama?->id_ruang;
        $indikatorId = $this->indikator;
        $tahun = $this->tahun ?? date('Y');

        $chartData = array_fill(0, 12, 0);
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        if ($ruanganId && $indikatorId) {
            $results = HospitalSurveyIndicatorResult::where('result_indicator_id', $indikatorId)
                ->where('result_department_id', $ruanganId)
                ->whereYear('result_period', $tahun)
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->result_period)->format('n');
                });
            for ($i = 1; $i <= 12; $i++) {
                $result = $results->get($i);
                $chartData[$i - 1] = $result ? $result->persentase : 0;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Capaian (%)',
                    'data' => $chartData,
                    'borderColor' => 'rgba(59, 130, 246, 0.8)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Pastikan mode chart adalah line chart
    }
}
