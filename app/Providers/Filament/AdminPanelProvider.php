<?php

namespace App\Providers\Filament;

use App\Filament\Pages\InputMutuIndikator;
use App\Filament\Pages\LaporanPendataanPerbulan;
use App\Filament\Pages\Rekapitulasi;
use App\Livewire\LoginController;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('app')
            ->login(LoginController::class) // ✅ PAKAI CLASS INI
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                InputMutuIndikator::class,
                Rekapitulasi::class,
                LaporanPendataanPerbulan::class, // ✅ TAMBAHKAN INI
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            // ✅ TAMBAHKAN NAVIGATION GROUPS
            ->navigationGroups([
                NavigationGroup::make('Laporan')
                    ->label('Laporan')
                    ->collapsible()
                    ->collapsed(false), // Default expand
            ])
            ->navigationItems([

                NavigationItem::make('Indikator Mutu Unit Anda')
                    ->group('Laporan')
                    ->url(fn (): string => Rekapitulasi::getUrl())
                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.rekapitulasi'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->sort(1),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                // AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
