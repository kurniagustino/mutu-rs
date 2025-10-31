<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        $this->call([
            UserSeeder::class,
            StatusKategoriSeeder::class,
            ImutCategorySeeder::class,
            UnitSeeder::class,    // WAJIB PERTAMA
            RuanganSeeder::class, // WAJIB KEDUA
        ]);
    }
}
