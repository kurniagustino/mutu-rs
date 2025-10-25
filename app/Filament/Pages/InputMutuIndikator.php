<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

    public $indicators = [];

    public $search = '';

    public $selectedIndicator = null;

    // Property untuk modal Persentase
    public $selectedIndicatorForReport = null;

    public $reportData = [];

    public $selectedYear = null;

    public $selectedMonth = null;

    // Property untuk modal Rekap
    public $selectedIndicatorForRekap = null;

    public $rekapData = [];

    public $rekapYear = null;

    public $rekapMonth = null;

    public $rekapStatistics = [];

    // Property baru untuk mode edit
    public $editMode = false;

    public $editResultId = null;

    // ✅ PROPERTY UNTUK FORM DATA
    public ?array $prosesData = [];

    public function mount(): void
    {
        $this->loadIndicators();
    }

    // ✅ METHOD getForms() UNTUK MULTIPLE FORMS
    protected function getForms(): array
    {
        return [
            'prosesDataForm',
        ];
    }

    public function loadIndicators(): void
    {
        $query = HospitalSurveyIndicator::query()
            ->with(['imutCategory', 'variables'])
            ->whereHas('departemens', function (Builder $query) {
                $user = Auth::user();
                if ($user && $user->departemen) {
                    $query->where('departemen.id_unit', $user->departemen->id_unit);
                }
            });

        if ($this->search) {
            $query->where('indicator_element', 'like', '%'.$this->search.'%');
        }

        $this->indicators = $query->get();
    }

    #[On('search-updated')]
    public function updatedSearch(): void
    {
        $this->loadIndicators();
    }

    // Update method openProsesModal untuk reset edit mode
    public function openProsesModal($indicatorId)
    {
        $this->selectedIndicator = HospitalSurveyIndicator::with('variables')->find($indicatorId);

        if (! $this->selectedIndicator) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Indikator tidak ditemukan')
                ->send();

            return;
        }

        // ✅ RESET MODE EDIT
        $this->editMode = false;
        $this->editResultId = null;

        $this->prosesData = [];
        $this->prosesDataForm->fill();

        $this->dispatch('open-modal', id: 'proses-data-modal');
    }

    public function prosesDataForm(Schema $schema): Schema
    {
        if (! $this->selectedIndicator) {
            return $schema
                ->components([])
                ->statePath('prosesData');
        }

        // ✅ CUMA TANGGAL AJA, SECTION NUMERATOR/DENOMINATOR DIHAPUS
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

    // ✅ METHOD SIMPAN DATA BIASA (SIMPAN & TUTUP MODAL)
    public function simpanData(): void
    {
        $this->simpanDataInternal(closeModal: true);
    }

    // ✅ METHOD BARU: SIMPAN & LANJUT (MODAL TETAP BUKA)
    public function simpanDanLanjut(): void
    {
        $this->simpanDataInternal(closeModal: false);
    }

    // Update method simpanDataInternal untuk handle edit
    private function simpanDataInternal(bool $closeModal = true): void
    {
        try {
            if (! $this->selectedIndicator) {
                throw new \Exception('Indikator tidak ditemukan');
            }

            $data = $this->prosesData;

            $totalNumerator = 0;
            $totalDenominator = 0;

            // Hitung total
            foreach ($this->selectedIndicator->variables->where('variable_type', 'N') as $variable) {
                $totalNumerator += $data["numerator_{$variable->id}"] ?? 0;
            }

            foreach ($this->selectedIndicator->variables->where('variable_type', 'D') as $variable) {
                $totalDenominator += $data["denominator_{$variable->id}"] ?? 0;
            }

            // ✅ HANDLE EDIT MODE
            if ($this->editMode && $this->editResultId) {
                // Update data existing - CARI BY result_id
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

                $hasilSimpan = $result;
                $notifTitle = '✅ Data Berhasil Diupdate';

            } else {
                // Create data baru (seperti biasa)
                $hasilSimpan = HospitalSurveyIndicatorResult::updateOrCreate(
                    [
                        'result_indicator_id' => $this->selectedIndicator->indicator_id,
                        'result_department_id' => Auth::user()->id_ruang,
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
                ->body("Persentase capaian: {$hasilSimpan->persentase}%")
                ->send();

            if ($closeModal) {
                // Tutup modal dan reset semua
                $this->dispatch('close-modal', id: 'proses-data-modal');
                $this->selectedIndicator = null;
                $this->prosesData = [];
                $this->editMode = false;
                $this->editResultId = null;

                // ✅ REFRESH DATA REKAP JIKA SEDANG BUKA
                if ($this->selectedIndicatorForRekap) {
                    $this->loadRekapData();
                }
            } else {
                // Modal tetap buka
                if ($this->editMode) {
                    // Jika edit mode, tetap di edit mode (tidak auto increment tanggal)
                    $this->prosesData = ['tanggal' => $data['tanggal']];
                    $this->prosesDataForm->fill(['tanggal' => $data['tanggal']]);
                } else {
                    // Jika create mode, auto increment tanggal
                    $currentDate = \Carbon\Carbon::parse($data['tanggal']);
                    $nextDate = $currentDate->addDay();

                    $this->prosesData = ['tanggal' => $nextDate->format('Y-m-d')];
                    $this->prosesDataForm->fill(['tanggal' => $nextDate->format('Y-m-d')]);
                }
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

    public function getHeaderActions(): array
    {
        return [];
    }

    public function editVariable($variableId = null)
    {
        if (! $variableId) {
            Notification::make()
                ->danger()
                ->title('Aksi Gagal')
                ->body('Tidak ada ID variabel yang terkirim.')
                ->send();

            return;
        }

        Notification::make()
            ->info()
            ->title('Edit Variable')
            ->body("Fitur edit variable ID: {$variableId} dalam development...")
            ->send();
    }

    // Method untuk buka modal Persentase
    public function openPersentaseModal($indicatorId)
    {
        $this->selectedIndicatorForReport = HospitalSurveyIndicator::with(['variables', 'imutCategory'])
            ->find($indicatorId);

        if (! $this->selectedIndicatorForReport) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Indikator tidak ditemukan')
                ->send();

            return;
        }

        // Set default ke tahun dan bulan sekarang
        $this->selectedYear = now()->year;
        $this->selectedMonth = null; // null = semua bulan

        $this->loadReportData();
        $this->dispatch('open-modal', id: 'persentase-modal');
    }

    // Method untuk load data laporan
    public function loadReportData()
    {
        if (! $this->selectedIndicatorForReport) {
            return;
        }

        $query = HospitalSurveyIndicatorResult::where('result_indicator_id', $this->selectedIndicatorForReport->indicator_id)
            ->where('result_department_id', Auth::user()->id_ruang)
            ->whereYear('result_period', $this->selectedYear);

        // Filter bulan jika dipilih
        if ($this->selectedMonth) {
            $query->whereMonth('result_period', $this->selectedMonth);
        }

        // Group by bulan dan hitung total
        $this->reportData = $query->orderBy('result_period')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->result_period)->format('m Y');
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

    // Method untuk update filter tahun
    public function updatedSelectedYear()
    {
        $this->loadReportData();
    }

    // Method untuk update filter bulan
    public function updatedSelectedMonth()
    {
        $this->loadReportData();
    }

    // Method untuk buka modal Rekap
    public function openRekapModal($indicatorId)
    {
        $this->selectedIndicatorForRekap = HospitalSurveyIndicator::with(['variables', 'imutCategory', 'departemens'])
            ->find($indicatorId);

        if (! $this->selectedIndicatorForRekap) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Indikator tidak ditemukan')
                ->send();

            return;
        }

        // Set default ke tahun dan bulan sekarang
        $this->rekapYear = now()->year;
        $this->rekapMonth = null; // null = semua bulan

        $this->loadRekapData();
        $this->dispatch('open-modal', id: 'rekap-modal');
    }

    public function loadRekapData()
    {
        if (! $this->selectedIndicatorForRekap) {
            return;
        }

        $user = Auth::user();

        $query = HospitalSurveyIndicatorResult::where('result_indicator_id', $this->selectedIndicatorForRekap->indicator_id)
            ->where('result_department_id', $user->id_ruang)
            ->whereYear('result_period', $this->rekapYear);

        if ($this->rekapMonth) {
            $query->whereMonth('result_period', $this->rekapMonth);
        }

        $results = $query->orderBy('result_period', 'asc')->get();

        // ✅ UBAH $item->id JADI $item->result_id
        $this->rekapData = $results->map(function ($item) use ($user) {
            return [
                'id' => $item->result_id, // ✅ GANTI DARI id KE result_id
                'tanggal' => \Carbon\Carbon::parse($item->result_period)->format('Y-m-d'),
                'unit' => $user->departemen->nama_unit ?? 'N/A',
                'numerator' => $item->result_numerator_value,
                'denominator' => $item->result_denumerator_value,
                'persentase' => $item->persentase,
            ];
        })->toArray();

        $this->rekapStatistics = $results->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->result_period)->format('m');
        })->map(function ($monthData, $month) {
            return [
                'bulan' => \Carbon\Carbon::createFromFormat('m', $month)->format('F'),
                'jumlah_pengisian' => $monthData->count(),
            ];
        })->toArray();
    }

    // Method untuk update filter tahun rekap
    public function updatedRekapYear()
    {
        $this->loadRekapData();
    }

    // Method untuk update filter bulan rekap
    public function updatedRekapMonth()
    {
        $this->loadRekapData();
    }

    public function openEditModal($resultId = null)
    {
        if (! $resultId) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('ID tidak ditemukan')
                ->send();

            return;
        }

        try {
            $result = HospitalSurveyIndicatorResult::where('result_id', $resultId)
                ->with('indicator.variables')
                ->first();

            if (! $result) {
                Notification::make()
                    ->danger()
                    ->title('Error')
                    ->body('Data tidak ditemukan')
                    ->send();

                return;
            }

            // Set mode edit
            $this->editMode = true;
            $this->editResultId = $resultId;
            $this->selectedIndicator = $result->indicator;

            // Load existing data ke form
            $this->prosesData = [
                'tanggal' => $result->result_period,
            ];

            // Load nilai numerator dan denominator untuk setiap variabel
            foreach ($this->selectedIndicator->variables as $variable) {
                if ($variable->variable_type === 'N') {
                    $this->prosesData["numerator_{$variable->id}"] = $result->result_numerator_value;
                } else {
                    $this->prosesData["denominator_{$variable->id}"] = $result->result_denumerator_value;
                }
            }

            $this->prosesDataForm->fill($this->prosesData);

            // ✅ TUTUP MODAL REKAP DULU
            $this->dispatch('close-modal', id: 'rekap-modal');

            // ✅ DELAY SEDIKIT BARU BUKA MODAL EDIT (pakai JS)
            $this->dispatch('open-edit-after-close');

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Gagal load data: '.$e->getMessage())
                ->send();
        }
    }

    // ✅ YANG BENAR UNTUK HAPUS DATA
    public function hapusData($resultId = null)
    {
        if (! $resultId) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('ID tidak ditemukan')
                ->send();

            return;
        }

        try {
            // ✅ CARI BY result_id (BUKAN id)
            $result = HospitalSurveyIndicatorResult::where('result_id', $resultId)->first();

            if (! $result) {
                throw new \Exception('Data tidak ditemukan');
            }

            $result->delete();

            Notification::make()
                ->success()
                ->title('✅ Data Berhasil Dihapus')
                ->send();

            // Refresh data rekap
            $this->loadRekapData();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('❌ Gagal Menghapus Data')
                ->body($e->getMessage())
                ->send();
        }
    }
}
