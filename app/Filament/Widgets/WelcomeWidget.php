<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static ?int $sort = 0;

    // ✅ FIX: Hapus 'static' - parent class tidak pakai static
    protected string $view = 'filament.widgets.welcome-widget';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user->name,
            'unit' => $user->ruangan_utama->nama_ruang ?? 'N/A', // 👈 PERBAIKAN
            'role' => $user->roles->first()->name ?? 'User',
        ];
    }
}
