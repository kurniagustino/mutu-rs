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
            'unit' => $user->departemen->nama_ruang ?? 'N/A',
            'role' => $user->roles->first()->name ?? 'User',
        ];
    }
}
