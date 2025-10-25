<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DistribusiKategoriChart;
use App\Filament\Widgets\StatusCapaianChart;
use App\Filament\Widgets\TopIndikatorChart;
use App\Filament\Widgets\TrendCapaianMutuChart;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class GrafikMutu extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.grafik-mutu';

    protected static ?string $navigationLabel = 'Grafik Mutu';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    public function getSubheading(): ?string
    {
        $user = Auth::user();
        $unitName = $user->departemen->nama_ruang ?? 'Tidak ada unit';

        return "Unit Anda: {$unitName} | RS Bhayangkara Jambi";
    }

    // ✅ REGISTER WIDGETS DI PAGE
    protected function getHeaderWidgets(): array
    {
        return [
            TrendCapaianMutuChart::class,
            TopIndikatorChart::class,
            DistribusiKategoriChart::class,
            StatusCapaianChart::class,
        ];
    }

    // ✅ LAYOUT 2 KOLOM
    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
