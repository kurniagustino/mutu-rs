<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalSurveyIndicatorResult extends Model
{
    protected $table = 'hospital_survey_indicator_result';

    protected $primaryKey = 'result_id';

    // ✅ ENABLE TIMESTAMPS (created_at & updated_at)
    public $timestamps = true;

    protected $fillable = [
        'result_indicator_id',
        'result_department_id',
        'result_numerator_value',
        'result_denumerator_value',
        'result_period',
        'result_post_date',
        'result_record_status',
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
     * Relasi ke Departemen
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'result_department_id', 'id_unit');
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
