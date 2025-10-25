<?php

// use App\Livewire\LoginController;
use Illuminate\Support\Facades\Route; // Pastikan baris ini ada di bagian atas

// ✅ REDIRECT ROOT KE LOGIN ADMIN
Route::get('/', function () {
    return redirect('/app/login');
});
