<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use App\Models\ImutCategory;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
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

    // Properti
    public $indicators = [];

    public $search = '';

    public $selectedIndicator = null;

    public $filterCategory = '';

    public $filterUnit = null; // ID Ruangan

    public $selectedIndicatorForReport = null;

    public $reportData = [];

    public $reportStatistics = [];

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

    // Properti untuk label unit login dan jumlah unit
    public $mainUserUnitLabel = '-';

    public $mainUserUnitCount = 0;

    public function mount(): void
    {
        $this->filterCategory = '';
        $this->loadIndicators();

        // Inisialisasi form
        $this->prosesDataForm->fill();

        // Inisialisasi unit user login dan total unitnya
        $this->setMainUserUnitInfo();
    }

    protected function setMainUserUnitInfo()
    {
        $user = Auth::user();
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->mainUserUnitLabel = '-';
            $this->mainUserUnitCount = 0;

            return;
        }

        // Ambil ruangan utama user (ruangan pertama milik user)
        // Jika ingin pakai ruangan tertentu (misal IT), pastikan model ruangans datanya benar
        $mainRuangan = $user->ruangans->first(fn ($ruangan) => isset($ruangan->unit));
        if ($mainRuangan && $mainRuangan->unit && $mainRuangan->unit->unit_name) {
            $this->mainUserUnitLabel = $mainRuangan->unit->unit_name;
        } else {
            // Fallback: ambil dari semua unit unik user, pakai pertama
            $allUserUnits = $user->ruangans->map(fn ($ruangan) => $ruangan->unit)->filter()->unique('id');
            $this->mainUserUnitLabel = $allUserUnits->first()?->unit_name ?? '-';
        }

        // Hitung unique unit milik user, +1 yang lain
        $allUnitIds = $user->ruangans->map(fn ($r) => $r->unit?->id)->filter()->unique();
        // (+N) => banyak unit - 1 unit utama
        $this->mainUserUnitCount = max(0, $allUnitIds->count() - 1);
    }

    protected function getForms(): array
    {
        return ['prosesDataForm'];
    }

    public function loadIndicators(): void
    {
        $user = Auth::user();

        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->indicators = collect();

            return;
        }

        $userUnitIds = $user->ruangans->pluck('id_unit')->unique()->toArray();

        $query = HospitalSurveyIndicator::query()
            ->with(['imutCategory', 'variables', 'units', 'statuses'])
            ->where(function (Builder $q) use ($userUnitIds) {
                $q->whereHas('units', function (Builder $uq) use ($userUnitIds) {
                    $uq->whereIn('unit.id', $userUnitIds);
                });
            });

        if ($this->filterCategory) {
            $query->whereHas('imutCategory', function (Builder $q) {
                $q->where('imut_name_category', $this->filterCategory);
            });
        }

        if ($this->filterUnit) {
            $query->whereHas('units', function (Builder $q) {
                $q->where('unit.id', $this->filterUnit);
            });
        }

        if ($this->search) {
            $query->where('indicator_name', 'like', '%'.$this->search.'%');
        }

        $indicators = $query->get();

        $sortOrder = [
            'wajib' => 1,
            'area klinis' => 2,
            'area manajerial' => 3,
            'lokal' => 4,
        ];

        $this->indicators = $indicators->sortBy(function ($indicator) use ($sortOrder) {
            $secondarySort = $indicator->indicator_name;
            $categoryName = $indicator->imutCategory?->imut_name_category;
            $primarySort = $sortOrder[strtolower($categoryName)] ?? 99;

            return $primarySort.'_'.$secondarySort;
        })->values();
    }

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

    /**
     * --------- Custom Unit Tooltip Logic Here -----------
     * Urutkan userUnits agar tooltip yang ditampilkan/dimunculkan duluan berasal dari unit/ruang user yang login (ruangan user didahulukan).
     */
    protected function getViewData(): array
    {
        $user = Auth::user();

        // Pastikan $user tidak null dan punya relasi ruangans
        $userUnits = collect();

        if ($user && $user->ruangans) {
            // Ambil ID unit yang dipakai ruangan user terlebih dahulu (urutkan by ruang user)
            $unitIdsByRuanganOrder = $user->ruangans
                ->map(fn ($ruangan) => $ruangan->unit ? $ruangan->unit->id : null)
                ->filter()
                ->unique()
                ->values(); // This gives the order of appearance of units according to user's ruangans

            // Ambil semua unit user tanpa duplikat (as collection)
            $userUnits = $user->ruangans
                ->map(fn ($ruangan) => $ruangan->unit)
                ->filter()
                ->unique('id');

            // Susun ulang sehingga unit yang muncul pertama kali pada ruang user (urut dari $unitIdsByRuanganOrder)
            $userUnits = $unitIdsByRuanganOrder
                ->map(fn ($unitId) => $userUnits->firstWhere('id', $unitId))
                ->filter(); // (in order)

            // Lalu tambahkan unit yang lain (jika ada) yang tidak ada pada ruangan user
            $otherUnits = $user->ruangans
                ->map(fn ($ruangan) => $ruangan->unit)
                ->filter(fn ($unit) => $unit && ! $unitIdsByRuanganOrder->contains($unit->id))
                ->unique('id')
                ->values();

            // Gabungkan urutan
            $userUnits = $userUnits->concat($otherUnits)->unique('id')->values();
        }

        $availableCategories = ImutCategory::query()
            ->whereNotNull('imut_name_category')
            ->where('imut_name_category', '!=', '')
            ->distinct()
            ->orderBy('imut_name_category')
            ->pluck('imut_name_category')
            ->filter()
            ->values();

        return [
            'availableCategories' => $availableCategories,
            'userUnits' => $userUnits,
        ];
    }

    // --- Logika Modal ---

    /**
     * ✅ FUNGSI INI DIISI
     * Untuk me-reset form dan data saat modal dibuka
     */
    public function openProsesModal($indicatorId)
    {
        $this->selectedIndicator = HospitalSurveyIndicator::with('variables')->find($indicatorId);
        $this->editMode = false;
        $this->editResultId = null;
        // Reset prosesData
        $this->prosesData = [];
        // Reset form dengan data default
        $this->prosesDataForm->fill([
            'tanggal_lapor' => now()->format('Y-m-d'),
            'id_indikator' => $indicatorId,
        ]);

        // Dispatch event untuk membuka modal (jika view Anda mendengarkannya)
        $this->dispatch('open-modal', id: 'proses-data-modal');
    }

    /**
     * ✅ FUNGSI INI DIISI
     * Mendefinisikan form untuk modal
     */
    public function prosesDataForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('prosesData') // Hubungkan ke properti $prosesData
            ->components([
                DatePicker::make('tanggal_lapor')
                    ->label('Tanggal Lapor/Input')
                    ->default(now())
                    ->required()
                    ->native(false) // Tampilan kalender yang lebih baik
                    ->closeOnDateSelection(),

                // Field tersembunyi untuk menyimpan ID
                Hidden::make('id_indikator'),
            ]);
    }

    public function simpanData(): void
    {
        $this->simpanDataInternal(true);
    }

    public function simpanDanLanjut(): void
    {
        $this->simpanDataInternal(false);
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Method internal untuk menyimpan data indikator
     */
    private function simpanDataInternal(bool $closeModal = true): void
    {
        // Validasi form - coba ambil dari form state, jika tidak ada ambil dari prosesData
        $formData = $this->prosesDataForm->getState();

        // Fallback: ambil dari prosesData langsung jika form state kosong
        if (empty($formData)) {
            $formData = $this->prosesData;
        }

        // Validasi data form
        if (empty($formData['id_indikator']) || empty($formData['tanggal_lapor'])) {
            Notification::make()
                ->title('Data tidak lengkap')
                ->body('Mohon lengkapi semua data yang diperlukan (tanggal lapor dan indikator)')
                ->danger()
                ->send();

            return;
        }

        // Validasi apakah ada indikator yang dipilih
        if (! $this->selectedIndicator) {
            Notification::make()
                ->title('Indikator tidak ditemukan')
                ->body('Silakan pilih indikator terlebih dahulu')
                ->danger()
                ->send();

            return;
        }

        // Validasi apakah ada variable yang diisi
        if (! $this->selectedIndicator->variables || $this->selectedIndicator->variables->isEmpty()) {
            Notification::make()
                ->title('Data variabel tidak ditemukan')
                ->body('Indikator ini tidak memiliki variabel')
                ->danger()
                ->send();

            return;
        }

        // Ambil user dan ruangan
        $user = Auth::user();
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            Notification::make()
                ->title('Akses ditolak')
                ->body('User tidak memiliki ruangan yang terhubung')
                ->danger()
                ->send();

            return;
        }

        // Ambil ruangan pertama user
        $ruangan = $user->ruangans->first();
        $ruanganId = (string) $ruangan->id_ruang; // result_department_id adalah string(20)

        // Kumpulkan semua numerator dan denominator
        $totalNumerator = 0;
        $totalDenominator = 0;

        foreach ($this->selectedIndicator->variables as $variable) {
            $key = ($variable->variable_type === 'N' ? 'numerator' : 'denominator').'_'.$variable->variable_id;
            $value = $this->prosesData[$key] ?? 0;

            // Konversi ke float untuk perhitungan
            $value = (float) $value;

            if ($variable->variable_type === 'N') {
                $totalNumerator += $value;
            } else {
                $totalDenominator += $value;
            }
        }

        // Validasi minimal ada data yang diisi
        if ($totalNumerator == 0 && $totalDenominator == 0) {
            Notification::make()
                ->title('Data tidak valid')
                ->body('Mohon isi minimal satu nilai variabel')
                ->warning()
                ->send();

            return;
        }

        // Konversi ke string untuk disimpan (max 10 chars)
        $numeratorStr = substr((string) $totalNumerator, 0, 10);
        $denominatorStr = substr((string) $totalDenominator, 0, 10);

        // Pastikan format tanggal benar (Y-m-d)
        $tanggalLapor = $formData['tanggal_lapor'];
        if (is_object($tanggalLapor) && method_exists($tanggalLapor, 'format')) {
            // Jika Carbon instance
            $tanggalLapor = $tanggalLapor->format('Y-m-d');
        } elseif (is_string($tanggalLapor)) {
            // Jika sudah string, pastikan format benar
            try {
                $tanggalLapor = \Carbon\Carbon::parse($tanggalLapor)->format('Y-m-d');
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Format tanggal tidak valid')
                    ->body('Mohon pilih tanggal yang valid')
                    ->danger()
                    ->send();

                return;
            }
        }

        try {
            // Cek apakah sudah ada data untuk periode yang sama (jika edit mode)
            if ($this->editMode && $this->editResultId) {
                // Update data yang sudah ada
                $result = HospitalSurveyIndicatorResult::find($this->editResultId);
                if ($result) {
                    $result->update([
                        'result_numerator_value' => $numeratorStr,
                        'result_denumerator_value' => $denominatorStr,
                        'result_period' => $tanggalLapor,
                        'result_post_date' => now(),
                        'last_edited_by' => (string) $user->id,
                    ]);

                    Notification::make()
                        ->title('Data berhasil diperbarui')
                        ->body('Data indikator berhasil diperbarui')
                        ->success()
                        ->send();
                }
            } else {
                // Buat data baru
                HospitalSurveyIndicatorResult::create([
                    'result_indicator_id' => $formData['id_indikator'],
                    'result_department_id' => $ruanganId,
                    'result_numerator_value' => $numeratorStr,
                    'result_denumerator_value' => $denominatorStr,
                    'result_period' => $tanggalLapor,
                    'result_post_date' => now(),
                    'result_record_status' => 'A',
                    'last_edited_by' => (string) $user->id,
                ]);

                Notification::make()
                    ->title('Data berhasil disimpan')
                    ->body('Data indikator berhasil disimpan')
                    ->success()
                    ->send();
            }

            // Tutup modal jika closeModal = true
            if ($closeModal) {
                $this->dispatch('close-modal', id: 'proses-data-modal');
                // Reset form dan data
                $this->prosesData = [];
                $this->selectedIndicator = null;
            } else {
                // Reset form untuk input berikutnya (simpan dan lanjut)
                $this->prosesData = [];
                $this->prosesDataForm->fill([
                    'tanggal_lapor' => now()->format('Y-m-d'),
                    'id_indikator' => $formData['id_indikator'],
                ]);
            }

            // Reload indicators untuk refresh tampilan jika diperlukan
            // $this->loadIndicators();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi kesalahan')
                ->body('Gagal menyimpan data: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ PERBAIKAN ERROR UTAMA
     * Fungsi ini harus mengembalikan array
     */
    public function getHeaderActions(): array
    {
        return []; // Kembalikan array kosong
    }

    public function editVariable($variableId = null)
    { /* ... */
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Membuka modal persentase dan load data
     */
    public function openPersentaseModal($indicatorId)
    {
        $this->selectedIndicatorForReport = HospitalSurveyIndicator::find($indicatorId);

        // Set default tahun dan bulan jika belum ada
        if (! $this->selectedYear) {
            $this->selectedYear = now()->year;
        }
        if (! $this->selectedMonth) {
            $this->selectedMonth = now()->month;
        }

        // Load data report
        $this->loadReportData();

        // Dispatch event untuk membuka modal
        $this->dispatch('open-modal', id: 'persentase-modal');
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Memuat data persentase berdasarkan indikator, tahun, dan bulan
     */
    public function loadReportData()
    {
        if (! $this->selectedIndicatorForReport) {
            $this->reportData = [];

            return;
        }

        $user = Auth::user();
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->reportData = [];

            return;
        }

        // Ambil ID ruangan user
        $ruanganIds = $user->ruangans->pluck('id_ruang')->map(fn ($id) => (string) $id)->toArray();

        // Query data hasil
        $query = HospitalSurveyIndicatorResult::query()
            ->where('result_indicator_id', $this->selectedIndicatorForReport->indicator_id)
            ->where('result_record_status', 'A')
            ->whereIn('result_department_id', $ruanganIds);

        // Filter berdasarkan tahun
        if ($this->selectedYear) {
            $query->whereYear('result_period', $this->selectedYear);
        }

        // Filter berdasarkan bulan
        if ($this->selectedMonth) {
            $query->whereMonth('result_period', $this->selectedMonth);
        }

        $results = $query->orderBy('result_period', 'desc')->get();

        // Kelompokkan data per hari
        $groupedData = $results->groupBy(function ($result) {
            return $result->result_period->format('Y-m-d');
        })->map(function ($group, $date) {
            $totalNumerator = $group->sum(fn ($r) => (float) ($r->result_numerator_value ?? 0));
            $totalDenominator = $group->sum(fn ($r) => (float) ($r->result_denumerator_value ?? 0));
            $persentase = $totalDenominator > 0 ? round(($totalNumerator / $totalDenominator) * 100, 2) : 0;

            return [
                'tanggal' => $date,
                'tanggal_formatted' => \Carbon\Carbon::parse($date)->format('d M Y'),
                'total_numerator' => $totalNumerator,
                'total_denominator' => $totalDenominator,
                'persentase' => $persentase,
                'jumlah_input' => $group->count(),
            ];
        })->values();

        // Format data untuk ditampilkan
        $this->reportData = $groupedData->toArray();

        // Hitung statistik keseluruhan
        $totalNumerator = $results->sum(fn ($r) => (float) ($r->result_numerator_value ?? 0));
        $totalDenominator = $results->sum(fn ($r) => (float) ($r->result_denumerator_value ?? 0));
        $rataRataPersentase = $totalDenominator > 0 ? round(($totalNumerator / $totalDenominator) * 100, 2) : 0;

        // Tambahkan statistik ke reportData atau buat properti baru
        $this->reportStatistics = [
            'total_records' => count($results),
            'total_numerator' => $totalNumerator,
            'total_denominator' => $totalDenominator,
            'rata_rata_persentase' => $rataRataPersentase,
            'min_persentase' => $groupedData->min('persentase') ?? 0,
            'max_persentase' => $groupedData->max('persentase') ?? 0,
        ];
    }

    public function updatedSelectedYear()
    {
        $this->loadReportData();
    }

    public function updatedSelectedMonth()
    {
        $this->loadReportData();
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Membuka modal rekap dan load data
     */
    public function openRekapModal($indicatorId)
    {
        $this->selectedIndicatorForRekap = HospitalSurveyIndicator::find($indicatorId);

        // Set default tahun dan bulan jika belum ada
        if (! $this->rekapYear) {
            $this->rekapYear = now()->year;
        }
        if (! $this->rekapMonth) {
            $this->rekapMonth = now()->month;
        }

        // Load data rekap
        $this->loadRekapData();

        // Dispatch event untuk membuka modal
        $this->dispatch('open-modal', id: 'rekap-modal');
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Memuat data rekap berdasarkan indikator, tahun, dan bulan
     */
    public function loadRekapData()
    {
        if (! $this->selectedIndicatorForRekap) {
            $this->rekapData = [];

            return;
        }

        $user = Auth::user();
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->rekapData = [];

            return;
        }

        // Ambil ID ruangan user
        $ruanganIds = $user->ruangans->pluck('id_ruang')->map(fn ($id) => (string) $id)->toArray();

        // Query data hasil
        $query = HospitalSurveyIndicatorResult::query()
            ->with(['editor'])
            ->where('result_indicator_id', $this->selectedIndicatorForRekap->indicator_id)
            ->where('result_record_status', 'A')
            ->whereIn('result_department_id', $ruanganIds)
            ->orderBy('result_period', 'desc')
            ->orderBy('result_post_date', 'desc');

        // Filter berdasarkan tahun
        if ($this->rekapYear) {
            $query->whereYear('result_period', $this->rekapYear);
        }

        // Filter berdasarkan bulan
        if ($this->rekapMonth) {
            $query->whereMonth('result_period', $this->rekapMonth);
        }

        $results = $query->get();

        // Format data untuk ditampilkan
        $this->rekapData = $results->map(function ($result) {
            $numerator = (float) ($result->result_numerator_value ?? 0);
            $denominator = (float) ($result->result_denumerator_value ?? 0);
            $persentase = $denominator > 0 ? round(($numerator / $denominator) * 100, 2) : 0;

            return [
                'result_id' => $result->result_id,
                'tanggal' => $result->result_period->format('Y-m-d'),
                'tanggal_formatted' => $result->result_period->format('d M Y'),
                'numerator' => $numerator,
                'denominator' => $denominator,
                'persentase' => $persentase,
                'tanggal_input' => $result->result_post_date->format('d M Y H:i'),
                'editor' => $result->editor->name ?? '-',
            ];
        })->toArray();

        // Hitung statistik
        $this->rekapStatistics = [
            'total_records' => count($this->rekapData),
            'total_numerator' => $results->sum(fn ($r) => (float) ($r->result_numerator_value ?? 0)),
            'total_denominator' => $results->sum(fn ($r) => (float) ($r->result_denumerator_value ?? 0)),
            'average_persentase' => count($this->rekapData) > 0
                ? round(
                    collect($this->rekapData)->sum('persentase') / count($this->rekapData),
                    2
                )
                : 0,
        ];
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
