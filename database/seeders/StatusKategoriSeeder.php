<?php

namespace Database\Seeders;

use App\Models\StatusKategori;
use Illuminate\Database\Seeder;

class StatusKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_status' => 'Indikator Mutu Nasional 2021', 'warna_badge' => '#3b82f6'], // Blue
            ['nama_status' => 'Indikator Mutu SKP', 'warna_badge' => '#3b82f6'], // Blue
            ['nama_status' => 'Indikator Mutu Nasional', 'warna_badge' => '#10b981'], // Green
            ['nama_status' => 'Indikator Mutu Prioritas', 'warna_badge' => '#f59e0b'], // Yellow
            ['nama_status' => 'Indikator Mutu Pilihan Unit', 'warna_badge' => '#06b6d4'], // Cyan
        ];

        foreach ($categories as $category) {
            StatusKategori::updateOrCreate(
                ['nama_status' => $category['nama_status']],
                $category
            );
        }
    }
}
