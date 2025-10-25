<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StatusKategori extends Model
{
    protected $table = 'status_kategori';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nama_status',
        'warna_badge',
    ];

    /**
     * Relasi Many-to-Many ke HospitalSurveyIndicator
     */
    public function indicators(): BelongsToMany
    {
        return $this->belongsToMany(
            HospitalSurveyIndicator::class,
            'indikator_status',
            'status_id',
            'indikator_id'
        );
    }
}
