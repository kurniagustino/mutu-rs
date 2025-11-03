<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use App\Models\ImutCategory;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
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

    // Detail modal
    public $selectedIndicatorId = null;

    public $detailData = [];

    public $showDetailModal = false;

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
                ->options(ImutCategory::pluck('imut_name_category', 'imut_category_id'))
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

        // ✅ PERBAIKAN: Ambil ID unit dari ruangan user
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->indicators = [];
            $this->showResults = true;

            return;
        }

        $userUnitIds = $user->ruangans->pluck('id_unit')->unique()->toArray();
        $ruanganIds = $user->ruangans->pluck('id_ruang')->map(fn ($id) => (string) $id)->toArray();

        // ✅ PERBAIKAN: Filter data menggunakan relasi units
        $this->indicators = HospitalSurveyIndicator::query()
            ->where('indicator_category_id', $this->imut_category_id)
            ->whereHas('units', function ($query) use ($userUnitIds) {
                $query->whereIn('unit.id', $userUnitIds);
            })
            ->with(['imutCategory', 'units'])
            ->withCount([
                'results as total_pendataan' => function ($query) use ($ruanganIds) {
                    $query->whereIn('result_department_id', $ruanganIds)
                        ->where('result_record_status', 'A')
                        ->whereYear('result_post_date', $this->tahun)
                        ->whereMonth('result_post_date', $this->bulan);
                },
            ])
            ->get()
            ->map(function ($indicator) {
                return [
                    'id' => $indicator->indicator_id,
                    'area' => $indicator->units->first()->nama_unit ?? 'N/A',
                    'title' => $indicator->indicator_name,
                    'type' => $indicator->indicator_type,
                    'total_pendataan' => $indicator->total_pendataan,
                ];
            });

        $this->showResults = true;
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();

        // ✅ PERBAIKAN: Gunakan ruangans
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            return 'Unit Anda: Tidak ada unit';
        }

        $ruangan = $user->ruangans->first();
        $unitName = $ruangan->nama_ruang ?? 'Tidak ada unit';

        return "Unit Anda: {$unitName}";
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Menampilkan detail indikator
     */
    public function showDetail($indicatorId): void
    {
        $this->selectedIndicatorId = $indicatorId;
        $this->loadDetailData();
        $this->showDetailModal = true;

        // Dispatch event untuk membuka modal
        $this->dispatch('open-modal', id: 'detail-modal');
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Memuat data detail indikator
     */
    public function loadDetailData(): void
    {
        if (! $this->selectedIndicatorId) {
            $this->detailData = [];

            return;
        }

        $user = Auth::user();

        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            $this->detailData = [];

            return;
        }

        $ruanganIds = $user->ruangans->pluck('id_ruang')->map(fn ($id) => (string) $id)->toArray();

        $indicator = HospitalSurveyIndicator::with(['imutCategory', 'units', 'variables'])
            ->find($this->selectedIndicatorId);

        if (! $indicator) {
            $this->detailData = [];

            return;
        }

        // Ambil data hasil untuk periode yang dipilih
        $results = HospitalSurveyIndicatorResult::query()
            ->where('result_indicator_id', $this->selectedIndicatorId)
            ->whereIn('result_department_id', $ruanganIds)
            ->where('result_record_status', 'A')
            ->whereYear('result_post_date', $this->tahun)
            ->whereMonth('result_post_date', $this->bulan)
            ->with(['editor'])
            ->orderBy('result_period', 'desc')
            ->orderBy('result_post_date', 'desc')
            ->get();

        // Format data detail
        $this->detailData = [
            'indicator' => [
                'id' => $indicator->indicator_id,
                'name' => $indicator->indicator_name,
                'type' => $indicator->indicator_type,
                'category' => $indicator->imutCategory->imut_name_category ?? 'N/A',
                'unit' => $indicator->units->first()->nama_unit ?? 'N/A',
                'target' => $indicator->indicator_target ?? '0',
                'satuan' => $indicator->satuan_pengukuran ?? 'N/A',
                'definition' => $indicator->indicator_definition,
            ],
            'results' => $results->map(function ($result) {
                $numerator = (float) ($result->result_numerator_value ?? 0);
                $denominator = (float) ($result->result_denumerator_value ?? 0);
                $persentase = $denominator > 0 ? round(($numerator / $denominator) * 100, 2) : 0;

                return [
                    'tanggal' => $result->result_period->format('Y-m-d'),
                    'tanggal_formatted' => $result->result_period->format('d M Y'),
                    'numerator' => $numerator,
                    'denominator' => $denominator,
                    'persentase' => $persentase,
                    'tanggal_input' => $result->result_post_date->format('d M Y H:i'),
                    'editor' => $result->editor->name ?? '-',
                ];
            })->toArray(),
            'statistics' => [
                'total_records' => $results->count(),
                'total_numerator' => $results->sum(fn ($r) => (float) ($r->result_numerator_value ?? 0)),
                'total_denominator' => $results->sum(fn ($r) => (float) ($r->result_denumerator_value ?? 0)),
                'rata_rata_persentase' => $results->count() > 0
                    ? round(
                        $results->sum(function ($r) {
                            $num = (float) ($r->result_numerator_value ?? 0);
                            $den = (float) ($r->result_denumerator_value ?? 0);

                            return $den > 0 ? ($num / $den) * 100 : 0;
                        }) / $results->count(),
                        2
                    )
                    : 0,
            ],
        ];
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Tutup modal detail
     */
    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedIndicatorId = null;
        $this->detailData = [];

        // Dispatch event untuk menutup modal
        $this->dispatch('close-modal', id: 'detail-modal');
    }

    /**
     * ✅ FUNGSI INI DIIMPLEMENTASIKAN
     * Cetak PDF laporan - buka di window baru
     */
    public function cetakPdf(): void
    {
        if (empty($this->indicators) || ! $this->showResults) {
            Notification::make()
                ->title('Tidak ada data')
                ->body('Silakan lihat laporan terlebih dahulu sebelum mencetak PDF')
                ->warning()
                ->send();

            return;
        }

        // Dispatch event untuk membuka window baru dengan view PDF
        $this->dispatch('open-pdf-window');
    }
}
