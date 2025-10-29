<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validasi extends Model
{
    protected $table = 'validasi';

    protected $fillable = [
        'imut',
        'periodevalidasi',
        'hasil_validasi',
        'analisa_text',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /**
     * Relasi ke HospitalSurveyIndicator
     */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(HospitalSurveyIndicator::class, 'imut', 'indicator_id');
    }

    /**
     * Relasi ke User yang validasi
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
