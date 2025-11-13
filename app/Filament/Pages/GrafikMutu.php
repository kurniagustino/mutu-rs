<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class GrafikMutu extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.grafik-mutu';

    protected static ?string $navigationLabel = 'Grafik Mutu';

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    // Properti untuk filter
    public $tahun;

    public $indikator;

    // Properti untuk menampung data dinamis
    public $indicatorOptions = [];

    public $unitName = 'Tidak ada unit';

    public $tableData = [];

    public $chartData = [];

    // Properti untuk statistik
    public $rataRataCapaian = 0;

    public $bulanTercapai = 0;

    public $dataMasuk = 0;

    public $capaianTertinggi = 0;

    public $target = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $this->tahun = date('Y');

        if ($user && $user->ruanganUtama) {
            $this->unitName = $user->ruanganUtama->nama_ruang;
            $ruanganId = $user->ruanganUtama->id_ruang ?? null;
            $this->indicatorOptions = [];
            if ($ruanganId) {
                // Ambil hanya indikator yang sudah diisi oleh ruangan user
                $submittedIndicatorIds = HospitalSurveyIndicatorResult::where('result_department_id', $ruanganId)
                    ->distinct()
                    ->pluck('result_indicator_id');
                $this->indicatorOptions = HospitalSurveyIndicator::whereIn('indicator_id', $submittedIndicatorIds)
                    ->pluck('indicator_name', 'indicator_id')
                    ->toArray();
            }
            // Set indikator default jika ada
            if (! empty($this->indicatorOptions)) {
                $this->indikator = array_key_first($this->indicatorOptions);
            }
        }

        $this->loadData();
    }

    public function updatedTahun(): void
    {
        $this->loadData();
    }

    public function updatedIndikator(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // Reset data
        $this->tableData = [];
        $this->chartData = [];
        $this->rataRataCapaian = 0;
        $this->bulanTercapai = 0;
        $this->dataMasuk = 0;
        $this->capaianTertinggi = 0;
        $this->target = 0;

        $user = Auth::user();
        if (! $user || ! $user->ruanganUtama || ! $this->indikator) {
            return;
        }

        $ruanganId = $user->ruanganUtama->id_ruang ?? null;
        $selectedIndicator = HospitalSurveyIndicator::find($this->indikator);
        $this->target = $selectedIndicator ? (float) $selectedIndicator->indicator_target : 0;

        $results = collect();
        if ($ruanganId) {
            $results = HospitalSurveyIndicatorResult::where('result_indicator_id', $this->indikator)
                ->where('result_department_id', $ruanganId)
                ->whereYear('result_period', $this->tahun)
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->result_period)->format('n');
                });
        }

        $totalCapaian = 0;
        $validMonths = 0;

        for ($i = 1; $i <= 12; $i++) {
            $bulan = Carbon::create()->month($i)->locale('id')->monthName;
            $result = $results->get($i);

            if ($result) {
                $capaian = $result->persentase;
                $status = $capaian >= $this->target ? 'Tercapai' : 'Tidak Tercapai';

                $this->tableData[] = [
                    'bulan' => $bulan,
                    'numerator' => $result->result_numerator_value,
                    'denominator' => $result->result_denumerator_value,
                    'capaian' => $capaian,
                    'status' => $status,
                ];

                $this->chartData[$i - 1] = $capaian;
                $totalCapaian += $capaian;
                $validMonths++;
                $this->dataMasuk++;

                if ($capaian > $this->capaianTertinggi) {
                    $this->capaianTertinggi = $capaian;
                }
                if ($capaian >= $this->target) {
                    $this->bulanTercapai++;
                }
            } else {
                $this->tableData[] = [
                    'bulan' => $bulan,
                    'numerator' => '-',
                    'denominator' => '-',
                    'capaian' => '-',
                    'status' => '-',
                ];
                $this->chartData[$i - 1] = 0;
            }
        }
        // Pastikan chartData selalu 12 elemen (Jan-Des)
        $this->chartData = array_values(array_pad($this->chartData, 12, 0));

        if ($validMonths > 0) {
            $this->rataRataCapaian = round($totalCapaian / $validMonths, 2);
        }

        // Dispatch event to update chart
        $this->dispatch('update-chart-data', chartData: $this->chartData, target: $this->target);
    }
}
