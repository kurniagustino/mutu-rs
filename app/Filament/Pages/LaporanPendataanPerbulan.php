<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\ImutCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class LaporanPendataanPerbulan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected string $view = 'filament.pages.laporan-pendataan-perbulan';

    protected static ?string $navigationLabel = 'Pendataan IMUT Perbulan';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    // Form data
    public ?int $imut_category_id = null;

    public ?int $bulan = null; // ✅ BULAN (1-12)

    public ?int $tahun = null; // ✅ TAHUN (2020-2030)

    // Results
    public $indicators = [];

    public $showResults = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('imut_category_id')
                ->label('IMUT Category')
                ->placeholder('Pilih IMUT')
                ->options(ImutCategory::pluck('imut', 'imut_category_id'))
                ->required()
                ->native(false)
                ->live(),

            // ✅ GANTI JADI SELECT BULAN
            Select::make('bulan')
                ->label('Bulan')
                ->placeholder('Pilih Bulan')
                ->options([
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ])
                ->required()
                ->native(false)
                ->live(),

            // ✅ SELECT TAHUN
            Select::make('tahun')
                ->label('Tahun')
                ->placeholder('Pilih Tahun')
                ->options(function () {
                    $currentYear = now()->year;
                    $years = [];
                    for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                        $years[$year] = $year;
                    }

                    return $years;
                })
                ->default(now()->year)
                ->required()
                ->native(false)
                ->live(),
        ];
    }

    protected function getFormColumns(): int
    {
        return 3; // ✅ 3 KOLOM (IMUT Category, Bulan, Tahun)
    }

    public function lihatLaporan(): void
    {
        $this->validate([
            'imut_category_id' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $user = Auth::user();
        $idRuang = $user->id_ruang;

        // ✅ Filter data untuk SELURUH BULAN yang dipilih
        $this->indicators = HospitalSurveyIndicator::query()
            ->where('indicator_category_id', $this->imut_category_id)
            ->whereHas('departemens', function ($query) use ($idRuang) {
                $query->where('id_ruang', $idRuang);
            })
            ->with(['imutCategory', 'departemens'])
            ->withCount([
                'results as total_pendataan' => function ($query) {
                    $query->whereYear('result_post_date', $this->tahun)
                        ->whereMonth('result_post_date', $this->bulan);
                },
            ])
            ->get()
            ->map(function ($indicator) {
                return [
                    'id' => $indicator->indicator_id,
                    'area' => $indicator->departemens->first()->nama_ruang ?? 'N/A',
                    'title' => $indicator->indicator_title,
                    'type' => $indicator->indicator_type,
                    'total_pendataan' => $indicator->total_pendataan,
                ];
            });

        $this->showResults = true;
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();
        $unitName = $user->departemen->nama_ruang ?? 'Tidak ada unit';

        return "Unit Anda: {$unitName}";
    }
}
