<?php

namespace App\Filament\Pages;

use App\Models\HospitalSurveyIndicator;
use App\Models\HospitalSurveyIndicatorResult;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Rekapitulasi extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected string $view = 'filament.pages.rekapitulasi';

    // ✅ UBAH LABEL JADI "Indikator Mutu Unit Anda" (untuk submenu)
    protected static ?string $navigationLabel = 'Indikator Mutu Unit Anda';

    // ✅ TETAP PAKAI NAVIGATION GROUP
    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    // ✅ TAMBAHKAN NAVIGATION PARENT ITEM (bikin jadi submenu)
    protected static ?string $navigationParentItem = 'Rekapitulasi';

    // ✅ DISABLE AUTO NAVIGATION REGISTRATION (karena kita custom di provider)
    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    // ✅ CUSTOM NAVIGATION LABEL UNTUK PARENT
    public static function getNavigationLabel(): string
    {
        return 'Indikator Mutu Unit Anda';
    }

    // Sub-heading
    public function getSubheading(): ?string
    {
        $user = Auth::user();

        // ✅ PERBAIKAN: Gunakan nama unit, bukan nama ruangan
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            return 'Unit Anda: Tidak ada unit';
        }

        $ruangan = $user->ruangans->first();
        $unitName = $ruangan->unit->nama_unit ?? $ruangan->nama_ruang ?? 'Tidak ada unit';

        return "Unit Anda: {$unitName}";
    }

    /**
     * Get indicator count grouped by date
     */
    public function getIndicatorsByDate()
    {
        $user = Auth::user();

        // ✅ PERBAIKAN: Ambil ID ruangan dari user
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            return collect();
        }

        $ruanganIds = $user->ruangans->pluck('id_ruang')->map(fn ($id) => (string) $id)->toArray();

        // ✅ PERBAIKAN: Filter berdasarkan result_department_id (ruangan)
        $indicators = HospitalSurveyIndicatorResult::query()
            ->whereIn('result_department_id', $ruanganIds)
            ->where('result_record_status', 'A')
            ->selectRaw('DATE(result_post_date) as date, COUNT(DISTINCT result_indicator_id) as count')
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->limit(4)
            ->get();

        return $indicators;
    }

    /**
     * Get all indicators for user's unit
     */
    public function getAllIndicators()
    {
        $user = Auth::user();

        // ✅ PERBAIKAN: Ambil ID unit dari ruangan user
        if (! $user || ! $user->ruangans || $user->ruangans->isEmpty()) {
            return collect();
        }

        $userUnitIds = $user->ruangans->pluck('id_unit')->unique()->toArray();

        // ✅ PERBAIKAN: Gunakan relasi units, bukan departemens
        $indicators = HospitalSurveyIndicator::query()
            ->whereHas('units', function ($query) use ($userUnitIds) {
                $query->whereIn('unit.id', $userUnitIds);
            })
            ->with(['imutCategory', 'units'])
            ->orderBy('indicator_id', 'DESC')
            ->get();

        return $indicators;
    }

    /**
     * Check if indicator has been completed today
     */
    public function checkIndicatorStatus($indicatorId)
    {
        // ✅ PAKAI result_indicator_id dan result_post_date
        $hasResult = HospitalSurveyIndicatorResult::query()
            ->where('result_indicator_id', $indicatorId)
            ->whereDate('result_post_date', today())
            ->exists();

        return $hasResult ? 'Sudah Dilaksanakan' : 'Belum Terlaksana';
    }
}
