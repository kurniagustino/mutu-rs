<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrasiImutCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrasi-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data imut_category_old ke imut_category (tabel baru)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi data ImutCategory...');

        // 1. Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel baru
        DB::table('imut_category')->truncate();
        $this->info('Tabel imut_category baru dikosongkan.');

        // 3. Data manual dari SQL dump (karena cuma 4 rows)
        $categories = [
            ['imut_category_id' => 1, 'imut' => 'AREA KLINIK'],
            ['imut_category_id' => 2, 'imut' => 'AREA MANAJEMEN'],
            ['imut_category_id' => 3, 'imut' => 'LOKAL'],
            ['imut_category_id' => 4, 'imut' => 'WAJIB'],
        ];

        $this->info('Memasukkan '.count($categories).' kategori...');

        // 4. Insert data
        $successCount = 0;
        $errorCount = 0;

        foreach ($categories as $category) {
            try {
                DB::table('imut_category')->insert($category);
                $this->info("✅ Berhasil memasukkan: {$category['imut']} (ID: {$category['imut_category_id']})");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("❌ Gagal memasukkan: {$category['imut']}. Error: ".$e->getMessage());
                $errorCount++;
            }
        }

        // 5. Enable foreign key checks
        Schema::enableForeignKeyConstraints();

        // 6. Summary
        $this->info('===================================================');
        $this->info('MIGRASI IMUT CATEGORY SELESAI!');
        $this->info("✅ Berhasil: {$successCount} records");

        if ($errorCount > 0) {
            $this->warn("❌ Gagal: {$errorCount} records");
        }

        $this->info('===================================================');

        // 7. Tampilkan data hasil migrasi
        $this->info('');
        $this->info('Data di tabel imut_category:');
        $results = DB::table('imut_category')->orderBy('imut_category_id')->get();

        $this->table(
            ['ID', 'Nama Kategori'],
            $results->map(fn ($r) => [$r->imut_category_id, $r->imut])
        );

        return 0;
    }
}
