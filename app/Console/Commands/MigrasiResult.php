<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data result (dari 2 tabel lama) ke hospital_survey_indicator_result (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data RESULT (Menggunakan metode CHUNK)...');

        Schema::disableForeignKeyConstraints();

        $namaTabelBaru = 'hospital_survey_indicator_result';
        $namaTabelLama_Aktif = 'hospital_survey_indicator_result_old';
        $namaTabelLama_Arsip = 'hospital_survey_indicator_result_old_arsip';
        $chunkSize = 1000; // Kita proses 1000 data sekali jalan
        $successCount = 0;
        $errorCount = 0;

        // 1. Kosongkan tabel baru
        DB::table($namaTabelBaru)->truncate();
        $this->info("Tabel {$namaTabelBaru} baru dikosongkan.");

        // 2. Tentukan field yang akan diambil
        $fieldsToMigrate = [
            'result_id',
            'result_indicator_id',
            'result_department_id',
            'result_numerator_value',
            'result_denumerator_value',
            'result_period',
            'result_post_date',
            'result_record_status',
            'last_edited_by',
        ];

        // 3. Hitung total data untuk progress bar
        $this->info('Menghitung total data...');
        $totalArsip = DB::table($namaTabelLama_Arsip)->count();
        $totalAktif = DB::table($namaTabelLama_Aktif)->count();
        $totalRecords = $totalArsip + $totalAktif;

        if ($totalRecords == 0) {
            $this->warn('Tidak ada data lama yang ditemukan. Selesai.');

            return;
        }

        $this->info("TOTAL data yang akan dimigrasi: {$totalRecords} records.");

        // 4. Siapkan progress bar
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        // 5. Proses TABEL ARSIP per chunk
        DB::table($namaTabelLama_Arsip)
            ->select($fieldsToMigrate)
            ->orderBy('result_id')
            // --- PERBAIKAN 1 DI SINI ---
            ->chunkById($chunkSize, function ($chunk) use ($namaTabelBaru, &$successCount, &$errorCount, $bar) {

                $dataToInsert = [];
                foreach ($chunk as $data) {
                    $dataToInsert[] = [
                        'result_id' => $data->result_id,
                        'result_indicator_id' => $data->result_indicator_id,
                        'result_department_id' => $data->result_department_id,
                        'result_numerator_value' => $data->result_numerator_value,
                        'result_denumerator_value' => $data->result_denumerator_value,
                        'result_period' => $data->result_period,
                        'result_post_date' => $data->result_post_date,
                        'result_record_status' => $data->result_record_status,
                        'last_edited_by' => $data->last_edited_by,
                    ];
                }

                try {
                    DB::table($namaTabelBaru)->insert($dataToInsert);
                    $successCount += count($dataToInsert);
                } catch (\Exception $e) {
                    $this->error("\n Gagal memasukkan 1 chunk (arsip). Error: ".$e->getMessage());
                    $errorCount += count($dataToInsert);
                }

                $bar->advance(count($chunk));
            }, 'result_id'); // <--- TAMBAHKAN NAMA KOLOM PK DI SINI

        // 6. Proses TABEL AKTIF per chunk
        DB::table($namaTabelLama_Aktif)
            ->select($fieldsToMigrate)
            ->orderBy('result_id')
            // --- PERBAIKAN 2 DI SINI ---
            ->chunkById($chunkSize, function ($chunk) use ($namaTabelBaru, &$successCount, &$errorCount, $bar) {

                $dataToInsert = [];
                foreach ($chunk as $data) {
                    $dataToInsert[] = [
                        'result_id' => $data->result_id,
                        'result_indicator_id' => $data->result_indicator_id,
                        'result_department_id' => $data->result_department_id,
                        'result_numerator_value' => $data->result_numerator_value,
                        'result_denumerator_value' => $data->result_denumerator_value,
                        'result_period' => $data->result_period,
                        'result_post_date' => $data->result_post_date,
                        'result_record_status' => $data->result_record_status,
                        'last_edited_by' => $data->last_edited_by,
                    ];
                }

                try {
                    DB::table($namaTabelBaru)->insert($dataToInsert);
                    $successCount += count($dataToInsert);
                } catch (\Exception $e) {
                    $this->error("\n Gagal memasukkan 1 chunk (aktif). Error: ".$e->getMessage());
                    $errorCount += count($dataToInsert);
                }

                $bar->advance(count($chunk));
            }, 'result_id'); // <--- TAMBAHKAN NAMA KOLOM PK DI SINI

        $bar->finish();
        $this->info("\n");

        Schema::enableForeignKeyConstraints();

        // 7. Summary
        $this->info('===================================================');
        $this->info('MIGRASI RESULT SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');
        $this->warn('PENTING: Jangan lupa jalankan SQL untuk update AUTO_INCREMENT tabel result!');
    }
}
