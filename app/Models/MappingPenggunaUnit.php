<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MappingPenggunaUnit extends Pivot
{
    protected $table = 'mapping_pengguna_unit';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $ruangan = Ruangan::find($model->id_ruang);
            if ($ruangan) {
                $model->id_unit = $ruangan->id_unit;
            }
        });

        static::saving(function ($model) {
            $ruangan = Ruangan::find($model->id_ruang);
            if ($ruangan) {
                $model->id_unit = $ruangan->id_unit;
            }
        });
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruang');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit');
    }
}