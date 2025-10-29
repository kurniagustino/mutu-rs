<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HsiResultValidasi extends Model
{
    use HasFactory;

    protected $table = 'hsi_result_validasi';

    protected $primaryKey = 'result_id';

    public $timestamps = false; // Tabel old tidak pakai created_at/updated_at

    protected $fillable = [
        'result_indicator_id',
        'result_department_id',
        'result_numerator_value',
        'result_denumerator_value',
        'rn_rekap_valid',
        'rd_rekap_valid',
        'validasi_pmkp',
        'result_period',
        'result_post_date',
        'result_record_status',
        'last_edited_by',
    ];

    protected $casts = [
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
     * Relasi ke Departemen
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'result_department_id', 'id_ruang');
    }

    /**
     * Relasi ke User yang edit
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by', 'id');
    }
}
