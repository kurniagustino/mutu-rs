<?php

namespace Database\Seeders;

use App\Models\ImutCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImutCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel dulu
        DB::table('imut_category')->truncate();

        $categories = [
            'wajib',
            'area klinis',
            'area manajerial',
            'lokal',
            'sasaran keselamatan pasien',
        ];

        // Looping dan buat data
        foreach ($categories as $category) {
            ImutCategory::create([
                // ✅ Sesuaikan dengan nama kolom di migrasi baru Anda
                'imut_name_category' => $category,
            ]);
        }
    }
}
