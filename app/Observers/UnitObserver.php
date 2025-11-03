<?php

namespace App\Observers;

use App\Models\Unit;
use App\Models\Ruangan;

class UnitObserver
{
    public function created(Unit $unit): void
    {
        // Buat ruangan otomatis dengan nama yang sama dengan unit
        Ruangan::create([
            'id_unit' => $unit->id,
            'nama_ruang' => $unit->nama_unit,
        ]);
    }
}