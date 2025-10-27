<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use BezhanSalleh\FilamentShield\Seeders\ShieldSeeder;
// --- TAMBAHKAN DUA BARIS INI ---
use Illuminate\Database\Seeder;

// ---------------------------------

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // PANGGIL USERSEEDER KITA DI SINI
        //    Ini akan membuat role 'super_admin', 'panel_user', dll.
        // $this->call(ShieldSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StatusKategoriSeeder::class);
    }
}
