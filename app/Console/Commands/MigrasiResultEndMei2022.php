<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiResultEndMei2022 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-result-endmei2022';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data dari hospital_survey_indicator_result_endmei2022 ke hospital_survey_indicator_result (Langkah 1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data hospital_survey_indicator_result_endmei2022...');

        $tableNameOld = 'hospital_survey_indicator_result_endmei2022';
        $tableNameNew = 'hospital_survey_indicator_result';

        // 1. Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru (HANYA UNTUK LANGKAH 1)
        DB::table($tableNameNew)->truncate();
        $this->info("Tabel {$tableNameNew} baru dikosongkan.");

        // 3. Ambil semua data dari tabel lama
        $oldData = DB::table($tableNameOld)
            ->select(
                'result_id',
                'result_indicator_id',
                'result_department_id',
                'result_numerator_value',
                'result_denumerator_value',
                'result_period',
                'result_post_date',
                'result_record_status',
                'last_edited_by'
            )
            ->orderBy('result_id')
            ->get();

        $this->info('Mengambil '.$oldData->count()." data dari {$tableNameOld}...");

        // 4. Copy data ke tabel baru
        $successCount = 0;
        $errorCount = 0;
        $bar = $this->output->createProgressBar($oldData->count());
        $bar->start();

        foreach ($oldData as $data) {
            try {
                // Konversi tanggal period
                $period = Carbon::parse($data->result_period)->toDateString();

                // === PERBAIKAN LOGIKA TANGGAL ===
                $postDateValue = $data->result_post_date;

                // Cek jika datanya invalid (null, '0000-...' atau string kosong)
                if (empty($postDateValue) || str_starts_with($postDateValue, '0000-00-00')) {
                    // Jika invalid, pakai tanggal 'result_period' sebagai gantinya
                    $this->warn("\n [ID: {$data->result_id}] 'result_post_date' invalid ({$postDateValue}). Fallback ke 'result_period' ({$period}).");
                    $postDate = Carbon::parse($period)->startOfDay()->toDateTimeString(); // Format: Y-m-d 00:00:00
                } else {
                    // Jika valid, parse seperti biasa
                    $postDate = Carbon::parse($postDateValue)->toDateTimeString();
                }
                // === AKHIR PERBAIKAN ===

                // Insert dengan ID yang sama (preserve primary key)
                DB::table($tableNameNew)->insert([
                    'result_id' => $data->result_id, // Keep ID sama!
                    'result_indicator_id' => $data->result_indicator_id,
                    'result_department_id' => $data->result_department_id,
                    'result_numerator_value' => $data->result_numerator_value,
                    'result_denumerator_value' => $data->result_denumerator_value,
                    'result_period' => $period,
                    'result_post_date' => $postDate, // Menggunakan $postDate yang sudah bersih
                    'result_record_status' => $data->result_record_status,
                    'last_edited_by' => $data->last_edited_by,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n Gagal memasukkan data ID: {$data->result_id}. Error: ".$e->getMessage());
                $errorCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n"); // New line after progress bar

        // 5. Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 6. Summary
        $this->info('===================================================');
        $this->info('MIGRASI LANGKAH 1 SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');

        return 0;
    }
}
