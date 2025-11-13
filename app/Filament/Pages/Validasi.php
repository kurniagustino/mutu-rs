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
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Validasi extends Page implements HasForms
{
    use InteractsWithForms;

    // ✅ BENAR: static untuk navigationIcon
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-check-circle';

    // ✅ PERBAIKAN: NON-STATIC untuk $view (tanpa kata "static")
    protected string $view = 'filament.pages.validasi';

    // ✅ BENAR: static untuk navigationGroup
    protected static string|UnitEnum|null $navigationGroup = 'PMKP';

    protected static ?int $navigationSort = 2;

    public $validationData = [];

    public $filterData = [];

    public $selectedCategory;

    public $selectedYear;

    public $selectedMonth;

    // Properties untuk modal validasi
    public $showValidasiModal = false;

    public $selectedIndicatorId = null;

    public $selectedIndicatorData = null;

    public $validasiForm = [
        'periode' => '',
        'numerator_rill' => '',
        'denominator_rill' => '',
        'numerator_sampling' => '',
        'denominator_sampling' => '',
        'status_cocok' => true,
    ];

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
                // FIX: Replace 'imut' with the correct category name column in ImutCategory::pluck
                // GANTI DENGAN KOLOM NAMA YANG BENAR, misal: 'imut_name_category'
                ->options(ImutCategory::pluck('imut_name_category', 'imut_category_id'))
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
            ->with(['imutCategory', 'units', 'results' => function ($q) use ($year, $month) {
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

            $periode = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT);

            $validasi = ValidasiModel::where('imut', $indicator->indicator_id)
                ->where('periodevalidasi', $periode)
                ->first();

            $unitName = $indicator->units->pluck('nama_unit')->implode(', ');

            // CATATAN: GUNAKAN KOLOM NAMA YANG BENAR DI imutCategory, misal: 'imut_name_category'
            return [
                'indicator_id' => $indicator->indicator_id,
                'indicator_name' => $indicator->indicator_definition,
                'category_name' => $indicator->imutCategory->imut_name_category ?? '-', // <-- KOLOM SESUAI TABEL
                'unit_name' => $unitName ?: 'N/A',
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

    // Method untuk modal validasi
    public function openValidasiModal($indicatorId)
    {
        $this->selectedIndicatorId = $indicatorId;

        // Cari data indicator dari validationData
        $indicatorData = collect($this->validationData)
            ->firstWhere('indicator_id', $indicatorId);

        if ($indicatorData) {
            $this->selectedIndicatorData = $indicatorData;

            $periode = $this->selectedYear.'-'.str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);

            // Load existing validation jika ada
            $existingValidasi = ValidasiModel::where('imut', $indicatorId)
                ->where('periodevalidasi', $periode)
                ->first();

            $this->validasiForm = [
                'periode' => $periode,
                'numerator_rill' => $existingValidasi ? $existingValidasi->nemurator : $indicatorData['numerator'],
                'denominator_rill' => $existingValidasi ? $existingValidasi->denumerator : $indicatorData['denominator'],
                'numerator_sampling' => $existingValidasi ? $existingValidasi->nemurator_sampling : '',
                'denominator_sampling' => $existingValidasi ? $existingValidasi->denumerator_sampling : '',
                'status_cocok' => $existingValidasi ? (bool) $existingValidasi->status_cocok : true,
            ];
        }

        $this->showValidasiModal = true;

        // ✅ TAMBAHAN: Dispatch event untuk buka modal (cara referensi)
        $this->dispatch('open-modal', id: 'validasi-modal');
    }

    public function closeValidasiModal()
    {
        $this->showValidasiModal = false;
        $this->selectedIndicatorId = null;
        $this->selectedIndicatorData = null;
        $this->resetValidasiForm();

        // ✅ TAMBAHAN: Dispatch event untuk tutup modal
        $this->dispatch('close-modal', id: 'validasi-modal');
    }

    private function resetValidasiForm()
    {
        $this->validasiForm = [
            'periode' => '',
            'numerator_rill' => '',
            'denominator_rill' => '',
            'numerator_sampling' => '',
            'denominator_sampling' => '',
            'status_cocok' => true,
        ];
    }

    public function saveValidasi()
    {
        // Validation
        $rules = [
            'validasiForm.numerator_rill' => 'required|numeric',
            'validasiForm.denominator_rill' => 'required|numeric',
        ];

        // Jika tidak cocok, sampling wajib diisi
        if (! $this->validasiForm['status_cocok']) {
            $rules['validasiForm.numerator_sampling'] = 'required|numeric';
            $rules['validasiForm.denominator_sampling'] = 'required|numeric';
        }

        $this->validate($rules);

        // Save or update
        ValidasiModel::updateOrCreate(
            [
                'imut' => $this->selectedIndicatorId,
                'periodevalidasi' => $this->validasiForm['periode'],
            ],
            [
                'nemurator' => $this->validasiForm['numerator_rill'],
                'denumerator' => $this->validasiForm['denominator_rill'],
                'nemurator_sampling' => $this->validasiForm['status_cocok'] ? null : $this->validasiForm['numerator_sampling'],
                'denumerator_sampling' => $this->validasiForm['status_cocok'] ? null : $this->validasiForm['denominator_sampling'],
                'hasil_validasi' => true,
                'status_cocok' => $this->validasiForm['status_cocok'],
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]
        );

        // Reload data
        $this->loadData();

        // Close modal
        $this->closeValidasiModal();

        // Success notification
        \Filament\Notifications\Notification::make()
            ->title('Validasi berhasil disimpan')
            ->success()
            ->send();
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
