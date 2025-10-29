<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiHsiResultValidasi extends Command
{
    protected $signature = 'app:migrasi-hsi-result-validasi';

    protected $description = 'Migrasi data hsi_result_validasi_old ke hsi_result_validasi';

    public function handle()
    {
        $this->info('Memulai migrasi data HSI Result Validasi...');

        // 1. Cek apakah tabel old ada
        if (! Schema::hasTable('hsi_result_validasi_old')) {
            $this->error('Tabel hsi_result_validasi_old tidak ditemukan!');
            $this->info('Silakan import SQL dump terlebih dahulu atau rename tabel lama ke hsi_result_validasi_old');

            return 1;
        }

        // 2. Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // 3. Kosongkan tabel baru
        DB::table('hsi_result_validasi')->truncate();
        $this->info('Tabel hsi_result_validasi dikosongkan.');

        // 4. Ambil data dari tabel old
        $resultsOld = DB::table('hsi_result_validasi_old')
            ->orderBy('result_id')
            ->get();

        $this->info('Mengambil '.$resultsOld->count().' data dari hsi_result_validasi_old...');

        // 5. Progress bar
        $bar = $this->output->createProgressBar($resultsOld->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($resultsOld as $result) {
            try {
                // Fix result_period format (0000-00-00 → null)
                $resultPeriod = $result->result_period;
                if ($resultPeriod === '0000-00-00' || empty($resultPeriod)) {
                    $resultPeriod = null;
                }

                // Fix result_post_date
                $postDate = $result->result_post_date;
                if ($postDate === '0000-00-00 00:00:00' || empty($postDate)) {
                    $postDate = now(); // Set ke tanggal sekarang
                }

                DB::table('hsi_result_validasi')->insert([
                    'result_id' => $result->result_id,
                    'result_indicator_id' => $result->result_indicator_id,
                    'result_department_id' => $result->result_department_id,
                    'result_numerator_value' => $result->result_numerator_value,
                    'result_denumerator_value' => $result->result_denumerator_value,
                    'rn_rekap_valid' => $result->rn_rekap_valid ?? null,
                    'rd_rekap_valid' => $result->rd_rekap_valid ?? null,
                    'validasi_pmkp' => $result->validasi_pmkp ?? null,
                    'result_period' => $resultPeriod,
                    'result_post_date' => $postDate,
                    'result_record_status' => $result->result_record_status ?? 'A',
                    'last_edited_by' => $result->last_edited_by ?? '0',
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$result->result_id}: ".$e->getMessage();
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 6. Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 7. Summary
        $this->info('===================================================');
        $this->info('MIGRASI HSI RESULT VALIDASI SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
            $this->newLine();
            $this->error('Detail Error:');
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($errors) > 10) {
                $this->line('  ... dan '.(count($errors) - 10).' error lainnya');
            }
        }

        $this->info('===================================================');

        // 8. Tampilkan sample data
        $this->info('');
        $this->info('Sample 5 data pertama:');
        $samples = DB::table('hsi_result_validasi')
            ->orderBy('result_id')
            ->limit(5)
            ->get();

        $this->table(
            ['ID', 'Indicator', 'Period', 'N', 'D', 'Valid N', 'Valid D'],
            $samples->map(fn ($r) => [
                $r->result_id,
                $r->result_indicator_id,
                $r->result_period ?? '-',
                $r->result_numerator_value ?? '0',
                $r->result_denumerator_value ?? '0',
                $r->rn_rekap_valid ?? '-',
                $r->rd_rekap_valid ?? '-',
            ])
        );

        return 0;

        // Di akhir method handle() di MigrasiHsiResultValidasi.php
        $maxId = DB::table('hsi_result_validasi')->max('result_id');
        DB::statement('ALTER TABLE hsi_result_validasi AUTO_INCREMENT = '.($maxId + 1));
        $this->info('✅ AUTO_INCREMENT di-set ke: '.($maxId + 1));

    }
}
