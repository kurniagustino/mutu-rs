<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\ImutCategory; // ✅ Tambahkan ImutCategory
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use UnitEnum;

class InputMutuIndikator extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Input Mutu Indikator';

    protected static string|UnitEnum|null $navigationGroup = 'Indikator Mutu';

    protected string $view = 'filament.pages.input-indikator';

    // Properti List
    public $indicators = [];

    public $search = '';

    public $selectedIndicator = null;

    // Filter Properties
    public $filterCategory = ''; // Default 'Semua'

    public $filterUnit = null;

    // ... (Properti modal lain) ...
    public $selectedIndicatorForReport = null;

    public $reportData = [];

    public $selectedYear = null;

    public $selectedMonth = null;

    public $selectedIndicatorForRekap = null;

    public $rekapData = [];

    public $rekapYear = null;

    public $rekapMonth = null;

    public $rekapStatistics = [];

    public $editMode = false;

    public $editResultId = null;

    public ?array $prosesData = [];

    public function mount(): void
    {
        $this->filterCategory = ''; // Tampilkan semua by default
        $this->loadIndicators();
    }

    protected function getForms(): array
    {
        return ['prosesDataForm'];
    }

    public function loadIndicators(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->departemens || $user->departemens->isEmpty()) {
            $this->indicators = collect();

            return;
        }
        $userUnitIds = $user->departemens->pluck('id_ruang')->toArray();

        $query = HospitalSurveyIndicator::query()
            ->with(['imutCategory', 'variables', 'departemens', 'statuses'])
            ->whereHas('departemens', function (Builder $q) use ($userUnitIds) {
                $q->whereIn('departemen.id_ruang', $userUnitIds);
            });

        // FILTER 1: Berdasarkan KATEGORI IMUT
        if ($this->filterCategory) {
            $query->whereHas('imutCategory', function (Builder $q) {
                // ✅ PERBAIKAN: Gunakan 'imut_name_category'
                $q->where('imut_name_category', $this->filterCategory);
            });
        }

        // FILTER 2: Berdasarkan UNIT
        if ($this->filterUnit) {
            $query->whereHas('departemens', function (Builder $q) {
                $q->where('departemen.id_ruang', $this->filterUnit);
            });
        }

        // FILTER 3: Berdasarkan PENCARIAN
        if ($this->search) {
            $query->where('indicator_element', 'like', '%'.$this->search.'%');
        }

        $indicators = $query->get();

        // Urutan prioritas
        $sortOrder = [
            'wajib' => 1,
            'area klinis' => 2,
            'area manajerial' => 3,
            'lokal' => 4,
        ];

        $this->indicators = $indicators->sortBy(function ($indicator) use ($sortOrder) {
            $secondarySort = $indicator->indicator_element;

            // ✅ PERBAIKAN: Gunakan 'imut_name_category'
            $categoryName = $indicator->imutCategory?->imut_name_category;

            $primarySort = $sortOrder[strtolower($categoryName)] ?? 99;

            return $primarySort.'_'.$secondarySort;
        })->values();
    } // ✅ PERBAIKAN: Kurung kurawal '}' ekstra dihapus dari sini

    // --- Hooks Filter ---
    #[On('search-updated')]
    public function updatedSearch(): void
    {
        $this->loadIndicators();
    }

    public function updatedFilterUnit(): void
    {
        $this->loadIndicators();
    }

    public function updatedFilterCategory(): void
    {
        $this->loadIndicators();
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        $userUnits = $user && $user->departemens ? $user->departemens : collect();

        // ✅ PERBAIKAN: Ambil dari 'imut_name_category'
        $availableCategories = ImutCategory::query()
            ->whereNotNull('imut_name_category')
            ->where('imut_name_category', '!=', '')
            ->distinct()
            ->orderBy('imut_name_category')
            ->pluck('imut_name_category') // ✅ Ganti kolom
            ->filter()
            ->values();

        return [
            'availableCategories' => $availableCategories,
            'userUnits' => $userUnits,
        ];
    }

    // --- Logika Modal (Tidak berubah) ---
    // (Semua method di bawah ini sudah benar dan tidak perlu diubah)

    public function openProsesModal($indicatorId)
    { /* ... */
    }

    public function prosesDataForm(Schema $schema): Schema
    { /* ... */
    }

    public function simpanData(): void
    { /* ... */
    }

    public function simpanDanLanjut(): void
    { /* ... */
    }

    private function simpanDataInternal(bool $closeModal = true): void
    { /* ... */
    }

    public function getHeaderActions(): array
    { /* ... */
    }

    public function editVariable($variableId = null)
    { /* ... */
    }

    public function openPersentaseModal($indicatorId)
    { /* ... */
    }

    public function loadReportData()
    { /* ... */
    }

    public function updatedSelectedYear()
    {
        $this->loadReportData();
    }

    public function updatedSelectedMonth()
    {
        $this->loadReportData();
    }

    public function openRekapModal($indicatorId)
    { /* ... */
    }

    public function loadRekapData()
    { /* ... */
    }

    public function updatedRekapYear()
    {
        $this->loadRekapData();
    }

    public function updatedRekapMonth()
    {
        $this->loadRekapData();
    }

    public function openEditModal($resultId = null)
    { /* ... */
    }

    public function hapusData($resultId = null)
    { /* ... */
    }
}
