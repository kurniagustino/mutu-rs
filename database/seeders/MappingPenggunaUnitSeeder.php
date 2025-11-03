<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MappingPenggunaUnitSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('mapping_pengguna_unit')->truncate();
        Schema::enableForeignKeyConstraints();

        // ✅ PERBAIKAN: Cari user 'promoter' (dari users.sql)
        $adminUser = User::where('username', 'promoter')->first();

        $allRuanganIds = Ruangan::pluck('id_ruang');

        $mappings = [];
        $now = now();

        if ($adminUser && $allRuanganIds->isNotEmpty()) {
            foreach ($allRuanganIds as $ruangId) {
                $mappings[] = [
                    'user_id' => $adminUser->id, // ID baru yang di-generate Laravel
                    'id_ruang' => $ruangId,
                    'level' => 'Admin PMKP',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($mappings)) {
            DB::table('mapping_pengguna_unit')->insert($mappings);
        }
    }
}
