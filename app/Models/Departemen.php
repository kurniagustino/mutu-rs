<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'departemen';

    protected $primaryKey = 'id_ruang';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_ruang',
        'nama_ruang',
    ];

    /**
     * Relasi ke Indikator (many-to-many)
     */
    public function indikator()
    {
        return $this->belongsToMany(
            \App\Models\HospitalSurveyIndicator::class,
            'mapping_indikator_unit',
            'id_unit',
            'id_indikator'
        );
    }

    /**
     * ✅ NEW: Relasi ke Users (many-to-many via mapping_pengguna_unit)
     */
    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'mapping_pengguna_unit',
            'id_ruang',
            'user_id',
            'id_ruang',
            'id'
        )
            ->withPivot('level')
            ->withTimestamps();
    }

    /**
     * Accessor untuk compatibility
     */
    public function getNamaUnitAttribute()
    {
        return $this->nama_ruang;
    }
}
