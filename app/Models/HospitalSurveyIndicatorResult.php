<?php

namespace App\Models; // ✅ SUDAH BENAR (pakai backslash \)

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalSurveyIndicatorResult extends Model
{
    // ✅ NAMA TABEL
    protected $table = 'hospital_survey_indicator_result';

    // ✅ PRIMARY KEY
    protected $primaryKey = 'result_id';

    // ✅ NONAKTIFKAN TIMESTAMPS
    public $timestamps = false;

    protected $fillable = [
        'result_indicator_id',
        'result_department_id',
        'result_period',
        'result_numerator_value',
        'result_denumerator_value',
        'result_post_date',
        'result_record_status',
        'last_edited_by',
    ];

    /**
     * Relasi ke HospitalSurveyIndicator
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(HospitalSurveyIndicator::class, 'result_indicator_id', 'indicator_id');
    }

    /**
     * Relasi ke Departemen (jika ada)
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'result_department_id', 'id_unit');
    }

    /**
     * Relasi ke User yang terakhir edit (jika ada)
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    /**
     * ============================================
     * ## 2. GANTI DENGAN BLOK LOGIKA INI ##
     * ============================================
     *
     * Accessor KLASIK untuk menghitung persentase secara otomatis.
     * Ini akan membuat properti "virtual" bernama $model->persentase
     */
    public function getPersentaseAttribute()
    {
        // Ambil nilai Ne dan De
        $nemu = (float) $this->result_numerator_value;
        $demu = (float) $this->result_denumerator_value;

        // Mencegah pembagian dengan nol (Division by Zero)
        if ($demu == 0) {
            return 0.00; // Jika demu 0, hasil otomatis 0
        }

        // Jika aman, jalankan formula
        $hasil = ($nemu / $demu) * 100;

        // Dibulatkan 2 angka di belakang koma
        return round($hasil, 2);
    }

}