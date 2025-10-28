<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiMappingPengguna extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-mapping-pengguna';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data mapping_pengguna_unit_old ke mapping_pengguna_unit (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data MAPPING PENGGUNA UNIT...');

        Schema::disableForeignKeyConstraints();

        // 3. Tentukan nama tabel
        $namaTabelBaru = 'mapping_pengguna_unit';
        $namaTabelLama = 'mapping_pengguna_unit_old';

        // 4. Kosongkan tabel baru
        DB::table($namaTabelBaru)->truncate();
        $this->info("Tabel {$namaTabelBaru} baru dikosongkan.");

        // 5. Tentukan field yang akan diambil dari tabel lama
        // Kita hanya ambil yang PASTI ADA
        $fieldsToMigrateAman = [
            'id_pegawai',
            'id_ruang',
        ];

        // 6. Ambil semua data dari tabel lama
        $mappingLama = DB::table($namaTabelLama)
            ->select($fieldsToMigrateAman) // Kita pakai $fieldsToMigrateAman
            ->whereNotNull('id_pegawai') // Hanya ambil yang ada ID pegawainya
            ->get();

        $this->info('Mengambil '.$mappingLama->count()." data dari {$namaTabelLama}...");

        // 7. Copy data ke tabel baru
        $successCount = 0;
        $errorCount = 0;

        $bar = $this->output->createProgressBar($mappingLama->count());
        $bar->start();

        foreach ($mappingLama as $data) {
            try {
                DB::table($namaTabelBaru)->insert([
                    'user_id' => $data->id_pegawai,
                    'id_ruang' => $data->id_ruang, // Ini sudah boleh null
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n Gagal memasukkan mapping untuk user ID: {$data->id_pegawai}. Error: ".$e->getMessage());
                $errorCount++;
            }
            $bar->advance();
        }

        $bar->finish();

        // --- INI PERBAIKAN TYPO DARI . JADI -> ---
        $this->info("\n");

        Schema::enableForeignKeyConstraints();

        // 8. Summary
        $this->info('===================================================');
        $this->info('MIGRASI MAPPING PENGGUNA SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');
        $this->warn('PENTING: Jangan lupa jalankan SQL untuk update AUTO_INCREMENT tabel mapping ini!');
    }
}
