<?php

namespace App\Observers;

use App\Models\Ruangan;

class RuanganObserver
{
    public function creating(Ruangan $ruangan)
    {
        // Pastikan id_unit terisi saat membuat ruangan baru
        if (!$ruangan->id_unit) {
            $ruangan->id_unit = request()->input('id_unit');
        }
    }
}