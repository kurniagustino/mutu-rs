<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiVariable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // 1. Ganti signature
    protected $signature = 'app:migrasi-variable';

    /**
     * The console command description.
     *
     * @var string
     */
    // 2. Ganti deskripsi
    protected $description = 'Migrasi data hospital_survey_indicator_variable_old ke hospital_survey_indicator_variable (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data VARIABLE INDIKATOR...');

        // Pola yang sama dari template Anda
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru (tujuan)
        $namaTabelBaru = 'hospital_survey_indicator_variable';
        $namaTabelLama = 'hospital_survey_indicator_variable_old'; // Sesuai rencana kita

        DB::table($namaTabelBaru)->truncate();
        $this->info("Tabel {$namaTabelBaru} baru dikosongkan.");

        // 3. Tentukan field yang akan diambil dari tabel lama
        // Kita ambil field-field yang ada di tabel LAMA
        $fieldsToMigrate = [
            'variable_id',
            'variable_indicator_id',
            'variable_name',
            'variable_type',
            'variable_unit_name', // INI PENTING, kita ambil untuk dipindah
            // 'variable_record_status' dan 'variable_post_date' kita abaikan
        ];

        // 4. Ambil semua data dari tabel lama
        $variableLama = DB::table($namaTabelLama)
            ->select($fieldsToMigrate)
            ->orderBy('variable_id') // Urutkan berdasarkan ID
            ->get();

        $this->info('Mengambil '.$variableLama->count()." data dari {$namaTabelLama}...");

        // 5. Copy data ke tabel baru (satu per satu)
        $successCount = 0;
        $errorCount = 0;

        // Siapkan progress bar
        $bar = $this->output->createProgressBar($variableLama->count());
        $bar->start();

        foreach ($variableLama as $data) {
            try {
                // Insert dengan ID yang sama (preserve primary key)
                DB::table($namaTabelBaru)->insert([
                    'variable_id' => $data->variable_id, // WAJIB SAMA!
                    'variable_indicator_id' => $data->variable_indicator_id,
                    'variable_name' => $data->variable_name,
                    'variable_type' => $data->variable_type, // Tipe char(1) dan enum('N','D') cocok

                    // --- INI LOGIKA UTAMA (TRANSFORMASI) ---
                    // Kita pindahkan data 'unit_name' (lama) ke 'description' (baru)
                    'variable_description' => $data->variable_unit_name,
                    // ------------------------------------
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n Gagal memasukkan variable ID: {$data->variable_id}. Error: ".$e->getMessage());
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
        $this->info('MIGRASI VARIABLE SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');
        $this->warn('PENTING: Jangan lupa jalankan SQL untuk update AUTO_INCREMENT tabel variable!');
    }
}
