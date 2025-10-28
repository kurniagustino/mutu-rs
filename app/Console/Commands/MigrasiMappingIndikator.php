<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiMappingIndikator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-mapping-indikator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data mapping_indikator_unit_old ke mapping_indikator_unit (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data MAPPING INDIKATOR UNIT...');

        Schema::disableForeignKeyConstraints();

        // 3. Tentukan nama tabel
        $namaTabelBaru = 'mapping_indikator_unit';
        $namaTabelLama = 'mapping_indikator_unit_old';

        // 4. Kosongkan tabel baru
        DB::table($namaTabelBaru)->truncate();
        $this->info("Tabel {$namaTabelBaru} baru dikosongkan.");

        // 5. Tentukan field yang akan diambil dari tabel lama
        $fieldsToMigrate = [
            'id',
            'id_unit',
            'id_indikator',
            'status',
            'created',
            'tahun',
            'statuspmkp',
            'tambahan',
        ];

        // 6. Ambil semua data dari tabel lama
        $mappingLama = DB::table($namaTabelLama)
            ->select($fieldsToMigrate)
            ->orderBy('id') // Urutkan berdasarkan ID
            ->get();

        $this->info('Mengambil '.$mappingLama->count()." data dari {$namaTabelLama}...");

        // 7. Copy data ke tabel baru (satu per satu)
        $successCount = 0;
        $errorCount = 0;

        $bar = $this->output->createProgressBar($mappingLama->count());
        $bar->start();

        foreach ($mappingLama as $data) {
            try {
                // Insert dengan ID yang sama
                DB::table($namaTabelBaru)->insert([
                    'id' => $data->id, // WAJIB SAMA!
                    'id_unit' => $data->id_unit,
                    'id_indikator' => $data->id_indikator,
                    'status' => $data->status,
                    'created' => $data->created,
                    'tahun' => $data->tahun,
                    'statuspmkp' => $data->statuspmkp,
                    'tambahan' => $data->tambahan, // Ini aman karena migrasi Bng sudah diperbaiki
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n Gagal memasukkan mapping ID: {$data->id}. Error: ".$e->getMessage());
                $errorCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");

        Schema::enableForeignKeyConstraints();

        // 8. Summary
        $this->info('===================================================');
        $this->info('MIGRASI MAPPING INDIKATOR SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');
        $this->warn('PENTING: Jangan lupa jalankan SQL untuk update AUTO_INCREMENT tabel mapping!');
    }
}
