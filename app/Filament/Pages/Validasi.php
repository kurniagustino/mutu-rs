<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\ImutCategory;
use App\Models\Validasi as ValidasiModel;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Validasi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Validasi';

    protected static string|UnitEnum|null $navigationGroup = 'PMKP';

    protected string $view = 'filament.pages.validasi';

    public ?array $filterData = [];

    public $selectedCategory = null;

    public $selectedYear = null;

    public $selectedMonth = null;

    public $validationData = [];

    public function mount(): void
    {
        $this->selectedYear = date('Y');
        $this->selectedMonth = date('m');
        $this->filterData = [
            'category_id' => null,
            'year' => $this->selectedYear,
            'month' => $this->selectedMonth,
        ];
        $this->loadData();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('category_id')
                ->label('Kategori Area')
                ->options(ImutCategory::pluck('imut', 'imut_category_id'))
                ->searchable()
                ->placeholder('Pilih Kategori Area')
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadData()),

            Select::make('year')
                ->label('Tahun')
                ->options(function () {
                    $currentYear = date('Y');
                    $years = [];
                    for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                        $years[$i] = $i;
                    }

                    return $years;
                })
                ->default(date('Y'))
                ->required()
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadData()),

            Select::make('month')
                ->label('Bulan')
                ->options([
                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                ])
                ->default(date('m'))
                ->required()
                ->reactive()
                ->afterStateUpdated(fn () => $this->loadData()),
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'filterData';
    }

    public function loadData(): void
    {
        $categoryId = $this->filterData['category_id'] ?? null;
        $year = $this->filterData['year'] ?? date('Y');
        $month = $this->filterData['month'] ?? date('m');

        $this->selectedCategory = $categoryId;
        $this->selectedYear = $year;
        $this->selectedMonth = $month;

        $query = HospitalSurveyIndicator::query()
            ->with(['imutCategory', 'results' => function ($q) use ($year, $month) {
                $q->whereYear('result_period', $year)
                    ->whereMonth('result_period', $month);
            }])
            ->when($categoryId, fn ($q) => $q->where('indicator_category_id', $categoryId))
            ->orderBy('urutan');

        $indicators = $query->get();

        $this->validationData = $indicators->map(function ($indicator) use ($year, $month) {
            $results = $indicator->results;
            $numerator = $results->map(fn ($r) => (int) $r->result_numerator_value)->sum();
            $denominator = $results->map(fn ($r) => (int) $r->result_denumerator_value)->sum();
            $persentase = $denominator > 0 ? round(($numerator / $denominator) * 100, 2) : 0;

            $periode = $year.'-'.$month;
            $validasi = ValidasiModel::where('imut', $indicator->indicator_id)
                ->where('periodevalidasi', $periode)
                ->first();

            return [
                'indicator_id' => $indicator->indicator_id,
                'indicator_name' => $indicator->indicator_definition,
                'category_name' => $indicator->imutCategory->imut ?? '-',
                'numerator' => $numerator,
                'numerator_description' => $indicator->indicator_numerator_description,
                'denominator' => $denominator,
                'denominator_description' => $indicator->indicator_denumerator_description,
                'persentase' => $persentase,
                'hasil_validasi' => $validasi ? ($validasi->hasil_validasi ? 'Sudah Validasi' : 'Belum validasi') : 'Belum validasi',
                'analisa' => $validasi && $validasi->analisa_text ? 'Sudah dianalisa' : 'Belum validasi',
            ];
        })->toArray();
    }

    public function openDetailModal($indicatorId)
    {
        // Hardcode URL - PASTI JALAN
        return redirect()->to(
            '/app/validasi-detail?'.http_build_query([
                'indicator' => $indicatorId,
                'year' => $this->selectedYear,
                'month' => $this->selectedMonth,
            ])
        );
    }

    // Placeholder untuk validasi (nanti)
    public function openValidasiModal($indicatorId)
    {
        // TODO: implement
    }

    public function getTitle(): string
    {
        return 'Tabel Data Indikator Mutu Bulan Periode '.
               $this->selectedYear.'-'.
               str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
