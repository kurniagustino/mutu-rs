<?php

namespace Database\Seeders;

use App\Models\HospitalSurveyIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IndicatorVariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('hospital_survey_indicator_variable')->truncate();
        Schema::enableForeignKeyConstraints();

        // 3. Peta data Numerator/Denominator dari PDF
        // Kunci array HARUS SAMA PERSIS dengan 'indicator_name' di seeder sebelumnya
        $variableMap = [
            'KEPATUHAN KEBERSIHAN TANGAN' => [
                'N' => 'Jumlah tindakan kebersihan tangan yang dilakukan',
                'D' => 'Jumlah total peluang kebersihan tangan yang seharusnya dilakukan dalam periode observasi',
            ],
            'KEPATUHAN PENGGUNAAN ALAT PELINDUNG DIRI' => [
                'N' => 'Jumlah petugas yang patuh menggunakan APD sesuai indikasi dalam periode observasi',
                'D' => 'Jumlah seluruh petugas yang terindikasi menggunakan APD dalam periode observasi',
            ],
            'KEPATUHAN IDENTIFIKASI PASIEN' => [
                'N' => 'Jumlah pemberi pelayanan yang melakukan identifikasi pasien secara benar dalam periode observasi',
                'D' => 'Jumlah pemberi pelayanan yang diobservasi dalam periode observasi',
            ],
            'KEPATUHAN WAKTU VISITE DOKTER' => [
                'N' => 'Jumlah pasien yang di-visite dokter pada pukul 06.00 14.00',
                'D' => 'Jumlah pasien yang diobservasi',
            ],
            'PELAPORAN HASIL KRITIS LABORATORIUM' => [
                'N' => 'Jumlah hasil kritis laboratorium yang dilaporkan ≤ 30 menit',
                'D' => 'Jumlah hasil kritis laboratorium yang diobservasi',
            ],
            'KEPATUHAN UPAYA PENCEGAHAN RESIKO PASIEN JATUH' => [
                'N' => 'Jumlah pasien rawat inap berisiko tinggi jatuh yang mendapatkan ketiga upaya pencegahan risiko jatuh',
                'D' => 'Jumlah pasien rawat inap berisiko tinggi jatuh yang diobservasi',
            ],
            'KEPATUHAN WAKTU TANGGAP TERHADAP KOMPLAIN' => [
                'N' => 'Jumlah komplain yang ditanggapi dan ditindaklanjuti sesuai waktu yang ditetapkan berdasarkan grading',
                'D' => 'Jumlah komplain yang disurvei',
            ],
            'KEPUASAN PASIEN' => [
                'N' => 'Total nilai persepsi seluruh responden',
                'D' => 'Total unsur yang terisi dari seluruh responden',
            ],
            'KEPATUHAN PENGGUNAAN ALUR KLINIS' => [
                'N' => 'Jumlah pelayanan oleh PPA yang sesuai dengan clinical pathway',
                'D' => 'Jumlah seluruh pelayanan oleh PPA pada clinical pathway yang diobservasi',
            ],
            'KEPATUHAN PENGUNAAN FORNAS' => [
                'N' => 'Jumlah R/ recipe dalam lembar resep yang sesuai dengan formularium nasional',
                'D' => 'Jumlah R/ recipe dalam lembar resep yang diobservasi',
            ],
            'WAKTU TUNGGU RAWAT JALAN' => [
                'N' => 'Jumlah pasien rawat jalan dengan waktu tunggu ≤ 60 menit',
                'D' => 'Jumlah pasien rawat jalan yang diobservasi',
            ],
            'WAKTU TANGGAP OPERASI SECIO CESARIA (EMERGENCY)' => [
                'N' => 'Jumlah pasien yang diputuskan tindakan seksio sesarea emergensi kategori I (satu) yang mendapatkan tindakan seksio sesarea emergensi ≤ 30 menit',
                'D' => 'Jumlah pasien yang diputuskan tindakan seksio sesarea emergensi kategori I',
            ],
            'PENUNDAAN OPERASI ELEKTIF' => [
                'N' => 'Jumlah pasien yang jadwal operasinya tertunda lebih dari 1 jam',
                'D' => 'Jumlah pasien operasi elektif',
            ],
            'Emergency Respon Time < 5 Menit' => [
                'N' => 'Jumlah Emergency Respon Time pada pasien Cito SC < 5 menit dalam 1 bulan',
                'D' => 'Jumlah Emergency Respon Time pada pasien Cito SC dalam 1 bulan',
            ],
            'Pencapaian sisa makanan pasien' => [
                'N' => 'Jumlah sisa makanan dari pasien yang tidak dihabiskan pada pasien',
                'D' => 'Jumlah seluruh sisa makanan pasien yang tidak di habiskan dalam 1 bulan',
            ],
            'ketepatan pemberian diit pasien' => [
                'N' => 'Jumlah pasien rawat inap yang dimendapat makanan tepat waktu',
                'D' => 'Jumlah seluruh pasien rawat inap dalam 1 bulan',
            ],
            // ... Tambahkan indikator lain di sini jika perlu
        ];

        // --- INI DIA PERBAIKANNYA ---

        // 1. Ambil SEMUA indikator (bukan di-pluck)
        $allIndicators = HospitalSurveyIndicator::select('indicator_id', 'indicator_name')->get();

        $variablesToInsert = [];

        // 2. Looping hasil query, BUKAN looping $variableMap
        foreach ($allIndicators as $indicator) {

            // 3. Cek apakah nama indikator ini ada di dalam $variableMap
            if (isset($variableMap[$indicator->indicator_name])) {

                // 4. Jika ada, ambil data N/D-nya
                $vars = $variableMap[$indicator->indicator_name];

                // Tambahkan data Numerator untuk ID indikator ini
                $variablesToInsert[] = [
                    'variable_indicator_id' => $indicator->indicator_id, // Gunakan ID yang sedang dilooping
                    'variable_name' => $vars['N'],
                    'variable_type' => 'N',
                    'variable_description' => 'Numerator (Pembilang)',
                ];

                // Tambahkan data Denominator untuk ID indikator ini
                $variablesToInsert[] = [
                    'variable_indicator_id' => $indicator->indicator_id, // Gunakan ID yang sedang dilooping
                    'variable_name' => $vars['D'],
                    'variable_type' => 'D',
                    'variable_description' => 'Denominator (Penyebut)',
                ];
            }
        }
        // --- BATAS PERBAIKAN ---

        // 5. Insert semua data sekaligus
        if (! empty($variablesToInsert)) {
            DB::table('hospital_survey_indicator_variable')->insert($variablesToInsert);
        }
    }
}
