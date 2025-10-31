<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // 👈 1. TAMBAH INI

class RuanganSeeder extends Seeder
{
    public function run()
    {
        // 2. TAMBAHKAN DUA BARIS INI
        Schema::disableForeignKeyConstraints();
        DB::table('ruangan')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Kita ambil semua unit BARU dari database
        $newUnits = DB::table('unit')->pluck('id', 'nama_unit');

        // 2. Buat 'peta' dari data LAMA
        // Peta: id_unit LAMA => nama_unit INDUK
        // Ini didapat dari analisa file .sql
        $oldUnitMap = [
            1 => 'Manajemen', 2 => 'Poliklinik', 3 => 'BPJS', 4 => 'Rekammedis',
            5 => 'Admission', 6 => 'IGD', 7 => 'Radiologi', 8 => 'Ruang Anak',
            9 => 'Instalasi Farmasi', 10 => 'Instalasi Kamar Operasi',
            11 => 'Laboratorium', 12 => 'ICU', 13 => 'Unit hemodialisa',
            14 => 'Kebidanan', 15 => 'Tim IT', 16 => 'Kasir', 17 => 'Keuangan',
            18 => 'Gudang', 19 => 'Gizi', 20 => 'FISIOTERAPI', 21 => 'Laundry',
            22 => 'IPRS', 23 => 'Perinatologi', 25 => 'Endoscopy',
            26 => 'DOKPOL', 27 => 'Supervisi',
        ];

        // 3. Semua 30 data ruangan LAMA
        $oldRuanganData = [
            ['id_unit_lama' => 1, 'nama_ruang' => 'Manajemen', 'sink' => null],
            ['id_unit_lama' => 2, 'nama_ruang' => 'Poliklinik', 'sink' => 'Poli'],
            ['id_unit_lama' => 3, 'nama_ruang' => 'BPJS', 'sink' => 'BPJS'],
            ['id_unit_lama' => 4, 'nama_ruang' => 'Rekammedis', 'sink' => 'RM'],
            ['id_unit_lama' => 5, 'nama_ruang' => 'Admission', 'sink' => null],
            ['id_unit_lama' => 6, 'nama_ruang' => 'IGD', 'sink' => null],
            ['id_unit_lama' => 7, 'nama_ruang' => 'Radiologi', 'sink' => null],
            ['id_unit_lama' => 8, 'nama_ruang' => 'Ruang Anak', 'sink' => 'R.Anak'],
            ['id_unit_lama' => 9, 'nama_ruang' => 'Instalasi Farmasi', 'sink' => null],
            ['id_unit_lama' => 10, 'nama_ruang' => 'Instalasi Kamar Operasi', 'sink' => 'OK'],
            ['id_unit_lama' => 11, 'nama_ruang' => 'Laboratorium', 'sink' => 'Lab'],
            ['id_unit_lama' => 12, 'nama_ruang' => 'ICU', 'sink' => null],
            ['id_unit_lama' => 13, 'nama_ruang' => 'Unit hemodialisa', 'sink' => 'HD'],
            ['id_unit_lama' => 14, 'nama_ruang' => 'Kebidanan', 'sink' => null],
            ['id_unit_lama' => 15, 'nama_ruang' => 'Tim IT', 'sink' => null],
            ['id_unit_lama' => 16, 'nama_ruang' => 'Kasir', 'sink' => null],
            ['id_unit_lama' => 17, 'nama_ruang' => 'Keuangan', 'sink' => null],
            ['id_unit_lama' => 18, 'nama_ruang' => 'Gudang', 'sink' => null],
            ['id_unit_lama' => 19, 'nama_ruang' => 'Gizi', 'sink' => 'Gizi'],
            ['id_unit_lama' => 8, 'nama_ruang' => 'RI BULIAN', 'sink' => 'RIA (BULIAN)'], // Anak dari 'Ruang Anak'
            ['id_unit_lama' => 8, 'nama_ruang' => 'RI TRIBRATA', 'sink' => 'RIB (TRIBRATA)'], // Anak dari 'Ruang Anak'
            ['id_unit_lama' => 20, 'nama_ruang' => 'FISIOTERAPI', 'sink' => null],
            ['id_unit_lama' => 21, 'nama_ruang' => 'Laundry', 'sink' => null],
            ['id_unit_lama' => 22, 'nama_ruang' => 'IPRS', 'sink' => null],
            ['id_unit_lama' => 9, 'nama_ruang' => 'Farmasi Ranap', 'sink' => 'Farm.Ranap'], // Anak dari 'Instalasi Farmasi'
            ['id_unit_lama' => 9, 'nama_ruang' => 'Farmasi Rajal', 'sink' => 'Farm.Poli'], // Anak dari 'Instalasi Farmasi'
            ['id_unit_lama' => 23, 'nama_ruang' => 'Perinatologi', 'sink' => 'PRT'],
            ['id_unit_lama' => 25, 'nama_ruang' => 'Endoscopy', 'sink' => null],
            ['id_unit_lama' => 26, 'nama_ruang' => 'DOKPOL', 'sink' => 'DOKPOL'],
            ['id_unit_lama' => 27, 'nama_ruang' => 'Supervisi', 'sink' => 'supervisi'],
        ];

        $now = Carbon::now();
        $ruanganToInsert = [];

        // 4. Loop data lama dan bangun relasi baru
        foreach ($oldRuanganData as $ruangan) {
            // 'id_unit_lama' => 9
            // $namaUnit = $oldUnitMap[9] => 'Instalasi Farmasi'
            $namaUnit = $oldUnitMap[$ruangan['id_unit_lama']];

            // $newIdUnit = $newUnits['Instalasi Farmasi'] => 9 (atau ID barunya)
            $newIdUnit = $newUnits[$namaUnit];

            $ruanganToInsert[] = [
                'nama_ruang' => $ruangan['nama_ruang'],
                'sink' => $ruangan['sink'],
                'id_unit' => $newIdUnit, // Ini dia relasinya, bang!
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 5. Masukkan semua data baru ke tabel ruangan
        DB::table('ruangan')->insert($ruanganToInsert);
    }
}
