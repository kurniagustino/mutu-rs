<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ValidasiDetail extends Page
{
    protected string $view = 'filament.pages.validasi-detail';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'validasi-detail';

    public $indicator;

    public $year;

    public $month;

    public $detailData = [];

    public function mount(): void
    {
        $this->indicator = request()->query('indicator');
        $this->year = request()->query('year', date('Y'));
        $this->month = request()->query('month', date('m'));

        $this->loadDetailData();
    }

    public function loadDetailData()
    {
        $indicator = HospitalSurveyIndicator::with([
            'imutCategory',
            'results' => function ($q) {
                $q->whereYear('result_period', $this->year)
                    ->whereMonth('result_period', $this->month)
                    ->where('result_record_status', 'A')
                    ->with('department');
            },
        ])->find($this->indicator);

        if (! $indicator) {
            $this->detailData = [];

            return;
        }

        $groupedResults = $indicator->results->groupBy('result_department_id')->map(function ($group) {
            $department = $group->first()->department;
            $numerator = $group->sum(fn ($r) => (int) $r->result_numerator_value);
            $denominator = $group->sum(fn ($r) => (int) $r->result_denumerator_value);
            $persentase = $denominator > 0 ? round(($numerator / $denominator) * 100, 2) : 0;

            return [
                'unit_name' => $department->nama_ruang ?? 'Unit Tidak Diketahui',
                'area_monitor' => $department->sink ?? '-',
                'numerator' => $numerator,
                'denominator' => $denominator,
                'persentase' => $persentase,
            ];
        });

        $this->detailData = [
            'indicator_id' => $indicator->indicator_id,
            'indicator_name' => $indicator->indicator_definition,
            'category_name' => $indicator->imutCategory->imut ?? '-',
            'monitoring_area' => $indicator->indicator_monitoring_area,
            'target' => $indicator->indicator_target ?? '100',
            'numerator_desc' => $indicator->indicator_numerator_description,
            'denominator_desc' => $indicator->indicator_denumerator_description,
            'results_by_unit' => $groupedResults->values()->toArray(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Indikator Mutu - Periode '.$this->year.'-'.str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }
}
