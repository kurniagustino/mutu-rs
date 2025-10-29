<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
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
use UnitEnum; // ✅ Tambahkan Carbon

class InputMutuIndikator extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Input Mutu Indikator';

    protected static string|UnitEnum|null $navigationGroup = 'Indikator Mutu';

    protected string $view = 'filament.pages.input-indikator'; // ✅ Path view Anda

    // Properti List
    public $indicators = [];

    public $search = '';

    public $selectedIndicator = null;

    // ✅ PROPERTI BARU UNTUK FILTER (SESUAI BLADE)
    public $filterArea = null;

    public $filterUnit = null;

    public $filterCategory = 'wajib'; // ✅ Default filter "wajib"

    // Properti modal Persentase
    public $selectedIndicatorForReport = null;

    public $reportData = [];

    public $selectedYear = null;

    public $selectedMonth = null;

    // Properti modal Rekap
    public $selectedIndicatorForRekap = null;

    public $rekapData = [];

    public $rekapYear = null;

    public $rekapMonth = null;

    public $rekapStatistics = [];

    // Properti mode edit
    public $editMode = false;

    public $editResultId = null;

    public ?array $prosesData = [];

    public function mount(): void
    {
        // ✅ Atur filter default saat halaman dimuat
        $this->filterCategory = 'wajib';
        $this->loadIndicators();
    }

    protected function getForms(): array
    {
        return [
            'prosesDataForm',
        ];
    }

    // ✅ ==========================================================
    // ✅ FUNGSI UTAMA YANG DIPERBAIKI (LOGIKA FILTER)
    // ✅ ==========================================================
    public function loadIndicators(): void
    {
        $user = Auth::user();

        // ✅ Gunakan relasi 'departemens' (jamak) yang baru dari Model User
        if (! $user || ! $user->departemens || $user->departemens->isEmpty()) {
            $this->indicators = collect();

            return;
        }

        // ✅ Ambil SEMUA ID unit yang dimiliki user
        $userUnitIds = $user->departemens->pluck('id_ruang')->toArray();

        $query = HospitalSurveyIndicator::query()
            ->with(['imutCategory', 'variables', 'departemens'])
            ->whereHas('departemens', function (Builder $q) use ($userUnitIds) {
                // ✅ Filter indikator yang terhubung ke SALAH SATU unit user
                $q->whereIn('departemen.id_ruang', $userUnitIds);
            });

        // ✅ FILTER 1: Berdasarkan KATEGORI (WAJIB, DLL)
        if ($this->filterCategory) {
            $query->whereHas('imutCategory', function (Builder $q) {
                // Asumsi nama 'wajib' ada di kolom 'imut'
                $q->where('imut', $this->filterCategory);
            });
        }

        // ✅ FILTER 2: Berdasarkan AREA MONITORING
        if ($this->filterArea) {
            $query->where('indicator_monitoring_area', $this->filterArea);
        }

        // ✅ FILTER 3: Berdasarkan UNIT SPESIFIK PILIHAN USER
        if ($this->filterUnit) {
            $query->whereHas('departemens', function (Builder $q) {
                $q->where('departemen.id_ruang', $this->filterUnit);
            });
        }

        // ✅ FILTER 4: Berdasarkan PENCARIAN
        if ($this->search) {
            $query->where('indicator_element', 'like', '%'.$this->search.'%');
        }

        $this->indicators = $query->get();
    }

    // ✅ METHOD BARU: Agar filter di Blade reaktif
    public function updatedFilterCategory(): void
    {
        $this->loadIndicators();
    }

    public function updatedFilterArea(): void
    {
        $this->loadIndicators();
    }

    public function updatedFilterUnit(): void
    {
        $this->loadIndicators();
    }

    // ✅ Method 'updatedSearch' sudah ada dari file Anda
    #[On('search-updated')]
    public function updatedSearch(): void
    {
        $this->loadIndicators();
    }

    // ==========================================================
    // ✅ METHOD BARU: Mengirim data ke Blade (PENTING!)
    // ==========================================================
    protected function getViewData(): array
    {
        $user = Auth::user();

        // ✅ Gunakan relasi 'departemens' (jamak)
        $userUnits = $user && $user->departemens ? $user->departemens : collect();
        $userUnitIds = $userUnits->pluck('id_ruang')->toArray();

        // Ambil area yang tersedia HANYA dari unit yang dimiliki user
        $availableAreas = HospitalSurveyIndicator::query()
            ->whereHas('departemens', function (Builder $q) use ($userUnitIds) {
                if (! empty($userUnitIds)) {
                    $q->whereIn('departemen.id_ruang', $userUnitIds);
                }
            })
            ->whereNotNull('indicator_monitoring_area')
            ->distinct()
            ->pluck('indicator_monitoring_area')
            ->filter()
            ->sort()
            ->values();

        // Ambil data kategori untuk filter dropdown (jika Anda mau menambahkannya)
        // $availableCategories = \App\Models\ImutCategory::query()
        //     ->orderBy('imut')
        //     ->pluck('imut')
        //     ->unique();

        return [
            'availableAreas' => $availableAreas,
            'userUnits' => $userUnits,
            // 'availableCategories' => $availableCategories, // Opsional
        ];
    }

    // ==========================================================
    // SISA KODE ANDA (LOGIKA MODAL) - TIDAK SAYA UBAH
    // ==========================================================

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

        // Isi form dengan data default (tanggal hari ini)
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

        return $schema
            ->components($fields)
            ->statePath('prosesData');
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
                throw new \Exception('Indikator tidak ditemukan');
            }

            // ✅ Ambil departemen pertama user untuk disimpan
            $userDepartmentId = Auth::user()->departemens->first()?->id_ruang;
            if (! $userDepartmentId) {
                throw new \Exception('User tidak memiliki departemen utama');
            }

            $data = $this->prosesDataForm->getState(); // ✅ Ambil data dari form

            $totalNumerator = 0;
            $totalDenominator = 0;

            // Hitung total dari data mentah di $this->prosesData (input manual)
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
                    throw new \Exception('Data tidak ditemukan');
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
                        'result_department_id' => $userDepartmentId, // ✅ Gunakan ID departemen
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

            Notification::make()
                ->success()
                ->title($notifTitle)
                ->body("Persentase capaian: {$persentase}%")
                ->send();

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
                // Reset form, tapi majukan tanggal 1 hari (jika bukan mode edit)
                $nextDate = $this->editMode ? $currentDate : $currentDate->addDay();

                $this->prosesData = ['tanggal' => $nextDate->format('Y-m-d')];
                $this->prosesDataForm->fill(['tanggal' => $nextDate->format('Y-m-d')]);
            }

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('❌ Gagal Menyimpan Data')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        }
    }

    // ... (Sisa fungsi Anda: openPersentaseModal, loadReportData, dll. tetap sama) ...
    // ... Saya salin sisa fungsi Anda di bawah ini tanpa perubahan ...

    public function getHeaderActions(): array
    {
        return [];
    }

    public function editVariable($variableId = null)
    {
        if (! $variableId) {
            Notification::make()->danger()->title('Aksi Gagal')->body('Tidak ada ID variabel yang terkirim.')->send();

            return;
        }
        Notification::make()->info()->title('Edit Variable')->body("Fitur edit variable ID: {$variableId} dalam development...")->send();
    }

    public function openPersentaseModal($indicatorId)
    {
        $this->selectedIndicatorForReport = HospitalSurveyIndicator::with(['variables', 'imutCategory'])->find($indicatorId);
        if (! $this->selectedIndicatorForReport) {
            Notification::make()->danger()->title('Error')->body('Indikator tidak ditemukan')->send();

            return;
        }
        $this->selectedYear = now()->year;
        $this->selectedMonth = null;
        $this->loadReportData();
        $this->dispatch('open-modal', id: 'persentase-modal');
    }

    public function loadReportData()
    {
        if (! $this->selectedIndicatorForReport) {
            return;
        }

        // ✅ Gunakan departemen pertama user
        $userDepartmentId = Auth::user()->departemens->first()?->id_ruang;
        if (! $userDepartmentId) {
            return;
        }

        $query = HospitalSurveyIndicatorResult::where('result_indicator_id', $this->selectedIndicatorForReport->indicator_id)
            ->where('result_department_id', $userDepartmentId) // ✅ Filter by departemen
            ->whereYear('result_period', $this->selectedYear);

        if ($this->selectedMonth) {
            $query->whereMonth('result_period', $this->selectedMonth);
        }

        $this->reportData = $query->orderBy('result_period')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->result_period)->format('m Y');
            })
            ->map(function ($monthData) {
                $totalNumerator = $monthData->sum('result_numerator_value');
                $totalDenominator = $monthData->sum('result_denumerator_value');

                return [
                    'bulan' => $monthData->first()->result_period,
                    'numerator' => $totalNumerator,
                    'denominator' => $totalDenominator,
                    'persentase' => $totalDenominator > 0 ? round(($totalNumerator / $totalDenominator) * 100, 2) : 0,
                    'count' => $monthData->count(),
                ];
            });
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
    {
        $this->selectedIndicatorForRekap = HospitalSurveyIndicator::with(['variables', 'imutCategory', 'departemens'])->find($indicatorId);
        if (! $this->selectedIndicatorForRekap) {
            Notification::make()->danger()->title('Error')->body('Indikator tidak ditemukan')->send();

            return;
        }
        $this->rekapYear = now()->year;
        $this->rekapMonth = null;
        $this->loadRekapData();
        $this->dispatch('open-modal', id: 'rekap-modal');
    }

    public function loadRekapData()
    {
        if (! $this->selectedIndicatorForRekap) {
            return;
        }

        $user = Auth::user();
        // ✅ Gunakan departemen pertama user
        $userDepartment = $user->departemens->first();
        if (! $userDepartment) {
            return;
        }

        $query = HospitalSurveyIndicatorResult::where('result_indicator_id', $this->selectedIndicatorForRekap->indicator_id)
            ->where('result_department_id', $userDepartment->id_ruang) // ✅ Filter by departemen
            ->whereYear('result_period', $this->rekapYear);

        if ($this->rekapMonth) {
            $query->whereMonth('result_period', $this->rekapMonth);
        }

        $results = $query->orderBy('result_period', 'asc')->get();

        $this->rekapData = $results->map(function ($item) use ($userDepartment) { // ✅ Gunakan $userDepartment
            // Hitung persentase lagi untuk keamanan
            $persentase = $item->result_denumerator_value > 0 ? round(($item->result_numerator_value / $item->result_denumerator_value) * 100, 2) : 0;

            return [
                'id' => $item->result_id,
                'tanggal' => Carbon::parse($item->result_period)->format('Y-m-d'),
                'unit' => $userDepartment->nama_unit ?? 'N/A', // ✅ Gunakan $userDepartment
                'numerator' => $item->result_numerator_value,
                'denominator' => $item->result_denumerator_value,
                'persentase' => $persentase, // ✅ Gunakan persentase
            ];
        })->toArray();

        $this->rekapStatistics = $results->groupBy(function ($item) {
            return Carbon::parse($item->result_period)->format('m');
        })->map(function ($monthData, $month) {
            return [
                'bulan' => Carbon::createFromFormat('m', $month)->format('F'),
                'jumlah_pengisian' => $monthData->count(),
            ];
        })->toArray();
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
    {
        if (! $resultId) {
            Notification::make()->danger()->title('Error')->body('ID tidak ditemukan')->send();

            return;
        }

        try {
            $result = HospitalSurveyIndicatorResult::where('result_id', $resultId)
                ->with('indicator.variables')
                ->first();

            if (! $result) {
                Notification::make()->danger()->title('Error')->body('Data tidak ditemukan')->send();

                return;
            }

            $this->editMode = true;
            $this->editResultId = $resultId;
            $this->selectedIndicator = $result->indicator;

            // Reset $prosesData
            $this->prosesData = [];
            $this->prosesData['tanggal'] = Carbon::parse($result->result_period)->format('Y-m-d');

            // Logika untuk mengisi data lama. Ini ASUMSI.
            // Jika Anda menyimpan JSON variabel, logikanya beda.
            // Kode ini mengasumsi 1 nilai N dan 1 nilai D per HARI.
            foreach ($this->selectedIndicator->variables as $variable) {
                if ($variable->variable_type === 'N') {
                    $this->prosesData["numerator_{$variable->id}"] = $result->result_numerator_value;
                } else {
                    $this->prosesData["denominator_{$variable->id}"] = $result->result_denumerator_value;
                }
            }

            $this->prosesDataForm->fill($this->prosesData);
            $this->dispatch('close-modal', id: 'rekap-modal');
            $this->dispatch('open-edit-after-close');

        } catch (\Exception $e) {
            Notification::make()->danger()->title('Error')->body('Gagal load data: '.$e->getMessage())->send();
        }
    }

    public function hapusData($resultId = null)
    {
        if (! $resultId) {
            Notification::make()->danger()->title('Error')->body('ID tidak ditemukan')->send();

            return;
        }
        try {
            $result = HospitalSurveyIndicatorResult::where('result_id', $resultId)->first();
            if (! $result) {
                throw new \Exception('Data tidak ditemukan');
            }

            $result->delete();
            Notification::make()->success()->title('✅ Data Berhasil Dihapus')->send();
            $this->loadRekapData();

        } catch (\Exception $e) {
            Notification::make()->danger()->title('❌ Gagal Menghapus Data')->body($e->getMessage())->send();
        }
    }
}
