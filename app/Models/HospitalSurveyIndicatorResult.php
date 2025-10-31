<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// 👈 1. TAMBAHKAN INI

class HospitalSurveyIndicatorResult extends Model
{
    use HasFactory;

    protected $table = 'hospital_survey_indicator_result';

    protected $primaryKey = 'result_id';

    public $timestamps = true;

    protected $fillable = [
        'result_indicator_id',
        'result_department_id',
        'result_period',
        'result_numerator_value',
        'result_denumerator_value',
        'result_record_status',
        'result_post_date',
        'last_edited_by',
    ];

    protected $casts = [
        'result_period' => 'date',
        'result_post_date' => 'datetime',
    ];

    /**
     * Relasi ke HospitalSurveyIndicator
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(HospitalSurveyIndicator::class, 'result_indicator_id', 'indicator_id');
    }

    /**
     * =============================================
     * ## 2. PERBAIKAN DI SINI ##
     * =============================================
     * Relasi ke Ruangan (menggantikan Departemen)
     */
    public function ruangan(): BelongsTo
    {
        // 'result_department_id' adalah foreign key di tabel INI
        // 'id_ruang' adalah primary key di tabel TUJUAN (ruangan)
        return $this->belongsTo(Ruangan::class, 'result_department_id', 'id_ruang');
    }

    /**
     * Relasi ke User yang edit
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    /**
     * Accessor untuk persentase
     */
    public function getPersentaseAttribute()
    {
        $nemu = (float) $this->result_numerator_value;
        $demu = (float) $this->result_denumerator_value;

        if ($demu == 0) {
            return 0.00;
        }

        $hasil = ($nemu / $demu) * 100;

        return round($hasil, 2);
    }
}
