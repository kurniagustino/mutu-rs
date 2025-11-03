<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <-- ADD THIS LINE

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Register observers
        \App\Models\Unit::observe(\App\Observers\UnitObserver::class);
        \App\Models\Ruangan::observe(\App\Observers\RuanganObserver::class);
    }
}
