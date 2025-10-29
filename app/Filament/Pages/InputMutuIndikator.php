<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use App\Models\ImutCategory; // ✅ Tambahkan ImutCategory
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
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
    // ✅ $filterArea diubah menjadi $filterCategory
    public $filterCategory = 'wajib'; // Default filter "wajib"

    public $filterUnit = null;

    // Modal Persentase
    public $selectedIndicatorForReport = null;

    public $reportData = [];

    public $selectedYear = null;

    public $selectedMonth = null;

    // Modal Rekap
    public $selectedIndicatorForRekap = null;

    public $rekapData = [];

    public $rekapYear = null;

    public $rekapMonth = null;

    public $rekapStatistics = [];

    // Edit Mode
    public $editMode = false;

    public $editResultId = null;

    public ?array $prosesData = [];

    public function mount(): void
    {
        // ✅ Pastikan filter category default ter-set
        $this->filterCategory = 'wajib';
        $this->loadIndicators();
    }

    protected function getForms(): array
    {
        return [
            'prosesDataForm',
        ];
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
            ->with(['imutCategory', 'variables', 'departemens'])
            ->whereHas('departemens', function (Builder $q) use ($userUnitIds) {
                $q->whereIn('departemen.id_ruang', $userUnitIds);
            });

        // ✅ FILTER 1: Berdasarkan KATEGORI IMUT (dari $filterCategory)
        if ($this->filterCategory) {
            $query->whereHas('imutCategory', function (Builder $q) {
                // Mencari di kolom 'imut' sesuai nilai $filterCategory
                $q->where('imut', $this->filterCategory);
            });
        }

        // ❌ FILTER Area Monitoring dihapus (karena diganti kategori)
        // if ($this->filterArea) { ... }

        // ✅ FILTER 2: Berdasarkan UNIT SPESIFIK PILIHAN USER
        if ($this->filterUnit) {
            $query->whereHas('departemens', function (Builder $q) {
                $q->where('departemen.id_ruang', $this->filterUnit);
            });
        }

        // ✅ FILTER 3: Berdasarkan PENCARIAN
        if ($this->search) {
            $query->where('indicator_element', 'like', '%'.$this->search.'%');
        }

        $this->indicators = $query->get();
    }

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

    // ✅ Method hook diganti namanya
    public function updatedFilterCategory(): void
    {
        $this->loadIndicators();
    }
    // ❌ Method hook lama dihapus
    // public function updatedFilterArea(): void { ... }

    protected function getViewData(): array
    {
        $user = Auth::user();
        $userUnits = $user && $user->departemens ? $user->departemens : collect();

        // ✅ Ambil data KATEGORI unik untuk dropdown filter
        $availableCategories = ImutCategory::query()
            ->whereNotNull('imut') // Pastikan tidak null
            ->where('imut', '!=', '') // Pastikan tidak string kosong
            ->distinct()
            ->orderBy('imut')
            ->pluck('imut')
            ->filter() // Hapus nilai kosong setelah pluck
            ->values();

        // ❌ Ambil data Area Monitoring tidak diperlukan lagi
        // $availableAreas = ...

        return [
            // ✅ Kirim $availableCategories ke view
            'availableCategories' => $availableCategories,
            'userUnits' => $userUnits,
            // ❌ $availableAreas tidak dikirim lagi
        ];
    }

    // --- Logika Modal ---
    // (Tidak ada perubahan di bawah ini, sama seperti kode Anda)

    public function openProsesModal($indicatorId)
    {
        $this->selectedIndicator = HospitalSurveyIndicator::with('variables')->find($indicatorId);
        if (! $this->selectedIndicator) {
            Notification::make()->danger()->title('Error')->body('Indikator tidak ditemukan')->send();

            return;
        }
        $this->editMode = false;
        $this->editResultId = null;
        $this->prosesData = [];
        $this->prosesDataForm->fill(['tanggal' => now()->format('Y-m-d')]);
        $this->dispatch('open-modal', id: 'proses-data-modal');
    }

    public function prosesDataForm(Schema $schema): Schema
    {
        $fields = [
            DatePicker::make('tanggal')
                ->label('📅 Tanggal Pengambilan Data')
                ->default(now())
                ->required()
                ->native(false),
        ];

        return $schema->components($fields)->statePath('prosesData');
    }

    public function simpanData(): void
    {
        $this->simpanDataInternal(closeModal: true);
    }

    public function simpanDanLanjut(): void
    {
        $this->simpanDataInternal(closeModal: false);
    }

    private function simpanDataInternal(bool $closeModal = true): void
    {
        try {
            if (! $this->selectedIndicator) {
                throw new \Exception('Indikator tidak dipilih');
            }

            $userDepartmentId = Auth::user()->departemens->first()?->id_ruang;
            if (! $userDepartmentId) {
                throw new \Exception('User tidak punya departemen utama');
            }

            $data = $this->prosesDataForm->getState();
            $totalNumerator = 0;
            $totalDenominator = 0;

            foreach ($this->selectedIndicator->variables->where('variable_type', 'N') as $variable) {
                $totalNumerator += $this->prosesData["numerator_{$variable->id}"] ?? 0;
            }
            foreach ($this->selectedIndicator->variables->where('variable_type', 'D') as $variable) {
                $totalDenominator += $this->prosesData["denominator_{$variable->id}"] ?? 0;
            }
            $persentase = $totalDenominator > 0 ? round(($totalNumerator / $totalDenominator) * 100, 2) : 0;

            if ($this->editMode && $this->editResultId) {
                $result = HospitalSurveyIndicatorResult::where('result_id', $this->editResultId)->first();
                if (! $result) {
                    throw new \Exception('Data tidak ditemukan untuk diupdate');
                }
                $result->update([
                    'result_period' => $data['tanggal'],
                    'result_numerator_value' => $totalNumerator,
                    'result_denumerator_value' => $totalDenominator,
                    'result_post_date' => now(),
                    'last_edited_by' => Auth::id(),
                ]);
                $notifTitle = '✅ Data Berhasil Diupdate';
            } else {
                HospitalSurveyIndicatorResult::updateOrCreate(
                    [
                        'result_indicator_id' => $this->selectedIndicator->indicator_id,
                        'result_department_id' => $userDepartmentId,
                        'result_period' => $data['tanggal'],
                    ],
                    [
                        'result_numerator_value' => $totalNumerator,
                        'result_denumerator_value' => $totalDenominator,
                        'result_post_date' => now(),
                        'last_edited_by' => Auth::id(),
                    ]
                );
                $notifTitle = '✅ Data Berhasil Disimpan';
            }

            Notification::make()->success()->title($notifTitle)->body("Capaian: {$persentase}%")->send();

            if ($closeModal) {
                $this->dispatch('close-modal', id: 'proses-data-modal');
                $this->selectedIndicator = null;
                $this->prosesData = [];
                $this->editMode = false;
                $this->editResultId = null;
                if ($this->selectedIndicatorForRekap) {
                    $this->loadRekapData();
                }
            } else {
                $currentDate = Carbon::parse($data['tanggal']);
                $nextDate = $this->editMode ? $currentDate : $currentDate->addDay();
                $this->prosesData = ['tanggal' => $nextDate->format('Y-m-d')]; // Reset N/D
                $this->prosesDataForm->fill(['tanggal' => $nextDate->format('Y-m-d')]);
            }

        } catch (\Exception $e) {
            Notification::make()->danger()->title('❌ Gagal Simpan')->body($e->getMessage())->persistent()->send();
        }
    }

    public function getHeaderActions(): array
    {
        return [];
    }

    public function editVariable($variableId = null)
    { /* ... implementasi ... */
    }

    public function openPersentaseModal($indicatorId)
    { /* ... implementasi ... */
    }

    public function loadReportData()
    { /* ... implementasi ... */
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
    { /* ... implementasi ... */
    }

    public function loadRekapData()
    { /* ... implementasi ... */
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
    { /* ... implementasi ... */
    }

    public function hapusData($resultId = null)
    { /* ... implementasi ... */
    }
}
