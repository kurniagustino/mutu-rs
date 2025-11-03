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
        $idUnit = $user->ruangan_utama->id_unit ?? null;
        $year = now()->year;

        if (!$idUnit) {
            return [
                'datasets' => [[
                    'data' => [0, 0],
                    'backgroundColor' => [
                        '#10b981',
                        '#ef4444',
                    ],
                ]],
                'labels' => ['Tercapai Target', 'Belum Tercapai'],
            ];
        }

        try {
            $results = HospitalSurveyIndicatorResult::whereYear('result_post_date', $year)
                ->whereHas('indicator.units', function ($q) use ($idUnit) {
                    $q->where('unit.id', $idUnit);
                })
                ->get();

            $total = $results->count();
            
            if ($total === 0) {
                return [
                    'datasets' => [[
                        'data' => [0, 0],
                        'backgroundColor' => [
                            '#10b981',
                            '#ef4444',
                        ],
                    ]],
                    'labels' => ['Tercapai Target', 'Belum Tercapai'],
                ];
            }

            // Hitung yang tercapai target (hitung persentase dan bandingkan dengan target)
            $achieved = $results->filter(function ($result) {
                $indicator = $result->indicator;
                if (!$indicator || !$indicator->indicator_target) {
                    return false;
                }
                
                // Hitung persentase
                $numerator = (float) ($result->result_numerator_value ?? 0);
                $denominator = (float) ($result->result_denumerator_value ?? 0);
                
                if ($denominator == 0) {
                    return false;
                }
                
                $persentase = ($numerator / $denominator) * 100;
                $target = (float) $indicator->indicator_target;
                
                return $persentase >= $target;
            })->count();

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
        } catch (\Exception $e) {
            return [
                'datasets' => [[
                    'data' => [0, 0],
                    'backgroundColor' => [
                        '#10b981',
                        '#ef4444',
                    ],
                ]],
                'labels' => ['Tercapai Target', 'Belum Tercapai'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getDescription(): ?string
    {
        return 'Status capaian target indikator mutu';
    }
}
