<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // 👈 1. TAMBAH INI

class UnitSeeder extends Seeder
{
    public function run()
    {
        // 2. TAMBAH 3 BARIS INI
        Schema::disableForeignKeyConstraints();
        DB::table('unit')->truncate();
        Schema::enableForeignKeyConstraints();

        $units = [
            ['nama_unit' => 'Manajemen'],
            ['nama_unit' => 'Poliklinik'],
            ['nama_unit' => 'BPJS'],
            ['nama_unit' => 'Rekammedis'],
            ['nama_unit' => 'Admission'],
            ['nama_unit' => 'IGD'],
            ['nama_unit' => 'Radiologi'],
            ['nama_unit' => 'Ruang Anak'],            // Induk untuk 'RI BULIAN'
            ['nama_unit' => 'Instalasi Farmasi'],    // Induk untuk 'Farmasi Ranap'
            ['nama_unit' => 'Instalasi Kamar Operasi'],
            ['nama_unit' => 'Laboratorium'],
            ['nama_unit' => 'ICU'],
            ['nama_unit' => 'Unit hemodialisa'],
            ['nama_unit' => 'Kebidanan'],
            ['nama_unit' => 'Tim IT'],
            ['nama_unit' => 'Kasir'],
            ['nama_unit' => 'Keuangan'],
            ['nama_unit' => 'Gudang'],
            ['nama_unit' => 'Gizi'],
            ['nama_unit' => 'FISIOTERAPI'],
            ['nama_unit' => 'Laundry'],
            ['nama_unit' => 'IPRS'],
            ['nama_unit' => 'Perinatologi'],
            ['nama_unit' => 'Endoscopy'],
            ['nama_unit' => 'DOKPOL'],
            ['nama_unit' => 'Supervisi'],
        ];

        // Masukkan data (ID akan auto-increment 1, 2, 3, ...)
        DB::table('unit')->insert($units);
    }
}
