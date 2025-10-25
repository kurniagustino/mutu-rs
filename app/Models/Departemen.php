<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';
    protected $primaryKey = 'id_ruang';
    public $timestamps = false;

    protected $fillable = [
        'nama_ruang',
        'id_unit',
        'sink',
    ];

    /**
     * Pastikan relasi ini ada dan bernama 'indicators'
     */
    public function indicators()
    {
        return $this->belongsToMany(\App\Models\HospitalSurveyIndicator::class, 'mapping_indikator_unit', 'id_unit', 'id_indikator');
    }

    // Tambahkan accessor ini
    public function getNamaUnitAttribute()
    {
        return $this->nama_ruang; // atau field yang benar
    }
}
