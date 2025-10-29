<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiValidasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-validasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data dari tabel validasi lama (CI) ke validasi baru (Laravel Filament)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===================================================');
        $this->info('Memulai migrasi data validasi...');

        // 1. Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru
        DB::table('validasi')->truncate();
        $this->info('Tabel validasi baru dikosongkan.');

        // 3. Ambil semua data dari tabel lama
        $validasiOld = DB::table('validasi_old')
            ->select(
                'id',
                'imut',
                'periodevalidasi'
            )
            ->orderBy('id')
            ->get();

        $this->info('Mengambil '.$validasiOld->count().' data dari validasi_old...');

        // 4. Copy data ke tabel baru
        $successCount = 0;
        $errorCount = 0;

        foreach ($validasiOld as $val) {
            try {
                // Insert dengan ID yang sama (preserve primary key)
                DB::table('validasi')->insert([
                    'id' => $val->id, // Keep ID sama!
                    'imut' => $val->imut,
                    'periodevalidasi' => $val->periodevalidasi,

                    // Field baru di Laravel, set default
                    'hasil_validasi' => false,
                    'analisa_text' => null,
                    'validated_at' => null,
                    'validated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $this->error("Gagal memasukkan validasi ID: {$val->id}. Error: ".$e->getMessage());
                $errorCount++;
            }
        }

        // 5. Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 6. Summary
        $this->info('===================================================');
        $this->info('MIGRASI SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");
        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }
        $this->info('===================================================');

        return 0;
    }
}
