<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiDepartemen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-departemen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data departemen_old ke departemen (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data departemen...');

        // 1. Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru
        DB::table('departemen')->truncate();
        $this->info('Tabel departemen baru dikosongkan.');

        // 3. Ambil semua data dari tabel lama
        $departemensOld = DB::table('departemen_old')
            ->select(
                'id_ruang',
                'id_unit',
                'nama_ruang',
                'sink'
            )
            ->orderBy('id_ruang')
            ->get();

        $this->info('Mengambil '.$departemensOld->count().' data dari departemen_old...');

        // 4. Copy data ke tabel baru
        $successCount = 0;
        $errorCount = 0;

        foreach ($departemensOld as $dept) {
            try {
                // Insert dengan ID yang sama (preserve primary key)
                DB::table('departemen')->insert([
                    'id_ruang' => $dept->id_ruang,  // Keep ID sama!
                    'id_unit' => $dept->id_unit,
                    'nama_ruang' => $dept->nama_ruang,
                    'sink' => $dept->sink,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("Gagal memasukkan departemen ID: {$dept->id_ruang} ({$dept->nama_ruang}). Error: ".$e->getMessage());
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
