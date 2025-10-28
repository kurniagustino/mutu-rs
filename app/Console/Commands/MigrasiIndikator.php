<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiIndikator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // 1. Ganti signature
    protected $signature = 'app:migrasi-indikator';

    /**
     * The console command description.
     *
     * @var string
     */
    // 2. Ganti deskripsi
    protected $description = 'Migrasi data hospital_survey_indicator_old ke hospital_survey_indicator (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data INDIKATOR...');

        // Pola yang sama dari template Anda
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru (tujuan)
        $namaTabelBaru = 'hospital_survey_indicator';
        $namaTabelLama = 'hospital_survey_indicator_old'; // Sesuai rencana kita

        DB::table($namaTabelBaru)->truncate();
        $this->info("Tabel {$namaTabelBaru} baru dikosongkan.");

        // 3. Tentukan field yang akan diambil dari tabel lama
        // (Semua field dari tabel LAMA kecuali 5 field status_imut_... yang Anda hapus)
        $fieldsToMigrate = [
            'indicator_id',
            'indicator_definition',
            'indicator_criteria_inclusive',
            'indicator_criteria_exclusive',
            'indicator_element',
            'indicator_element_2021',
            'indicator_source_of_data',
            'indicator_type',
            'indicator_value_standard',
            'indicator_monitoring_area',
            'indicator_frequency', // Ini yang akan kita transform
            'indicator_target',
            'indicator_category_id', // Field ini ada di tabel lama dan baru
            'indicator_iscomplete',
            'indicator_record_status',
            'status_kunci',
            'tampil_survey',
            'kategori',
            'urutan',
            'type_persen',
            'imut_must_valid',
            'files',
        ];

        // 4. Ambil semua data dari tabel lama
        $indikatorLama = DB::table($namaTabelLama)
            ->select($fieldsToMigrate)
            ->orderBy('indicator_id') // Urutkan berdasarkan ID
            ->get();

        $this->info('Mengambil '.$indikatorLama->count()." data dari {$namaTabelLama}...");

        // 5. Copy data ke tabel baru (satu per satu, sama seperti template Anda)
        $successCount = 0;
        $errorCount = 0;

        // Siapkan progress bar
        $bar = $this->output->createProgressBar($indikatorLama->count());
        $bar->start();

        foreach ($indikatorLama as $data) {
            try {
                // Insert dengan ID yang sama (preserve primary key)
                DB::table($namaTabelBaru)->insert([
                    'indicator_id' => $data->indicator_id, // WAJIB SAMA!
                    'indicator_definition' => $data->indicator_definition,
                    'indicator_criteria_inclusive' => $data->indicator_criteria_inclusive,
                    'indicator_criteria_exclusive' => $data->indicator_criteria_exclusive,
                    'indicator_element' => $data->indicator_element,
                    'indicator_element_2021' => $data->indicator_element_2021,
                    'indicator_source_of_data' => $data->indicator_source_of_data,
                    'indicator_type' => $data->indicator_type,
                    'indicator_value_standard' => $data->indicator_value_standard,
                    'indicator_monitoring_area' => $data->indicator_monitoring_area,

                    // --- INI LOGIKA TRANSFORMASI ---
                    'indicator_frequency' => $this->transformFrequency($data->indicator_frequency),
                    // -----------------------------

                    'indicator_target' => $data->indicator_target,
                    'indicator_category_id' => $data->indicator_category_id,
                    'indicator_iscomplete' => $data->indicator_iscomplete,
                    'indicator_record_status' => $data->indicator_record_status,
                    'status_kunci' => $data->status_kunci,
                    'tampil_survey' => $data->tampil_survey,
                    'kategori' => $data->kategori,
                    'urutan' => $data->urutan,
                    'type_persen' => $data->type_persen,
                    'imut_must_valid' => $data->imut_must_valid,
                    'files' => $data->files,

                    // 5 field status_imut_... otomatis DIABAIKAN
                    // karena tidak kita 'select' dan tidak kita 'insert'
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n Gagal memasukkan indikator ID: {$data->indicator_id}. Error: ".$e->getMessage());
                $errorCount++;
            }
            $bar->advance(); // Majukan progress bar
        }

        $bar->finish(); // Selesaikan progress bar
        $this->info("\n"); // Baris baru

        // 6. Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 7. Summary (Sama seperti template Anda)
        $this->info('===================================================');
        $this->info('MIGRASI INDIKATOR SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');
        $this->warn('PENTING: Jangan lupa jalankan SQL untuk update AUTO_INCREMENT tabel indikator!');
    }

    /**
     * Fungsi helper untuk transformasi data 'frequency'
     *
     * @param  string|null  $value
     * @return string|null
     */
    private function transformFrequency($value)
    {
        // $value adalah char(1) dari tabel lama
        switch ($value) {
            case 'M':
                return 'Bulanan';
            case 'B':
                return 'Bulanan';
            case 'H':
                return 'Harian';
            case 'D':
                return 'Harian';
            default:
                // Jika ada nilai lain (misal 'T' untuk Tahunan),
                // Anda bisa tambahkan di sini.
                // Jika tidak, kembalikan nilai aslinya.
                return $value;
        }
    }
}
