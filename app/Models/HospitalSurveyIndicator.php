<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalSurveyIndicator extends Model
{
    use HasFactory;

    protected $table = 'hospital_survey_indicator';

    protected $primaryKey = 'indicator_id';

    public $timestamps = false;

    /**
     * Kolom yang boleh diisi dari form.
     */
    protected $fillable = [
        'indicator_definition',
        'indicator_criteria_inclusive',
        'indicator_criteria_exclusive',
        'indicator_element',
        'indicator_element_2021',
        'indicator_source_of_data',
        'indicator_type',
        'indicator_value_standard',
        'indicator_monitoring_area',
        'indicator_frequency',
        'indicator_target',
        'indicator_category_id',
        'indicator_imut_type', // ✅ TAMBAHKAN INI
        'indicator_iscomplete',
        'indicator_record_status',
        'status_kunci',
        'status_imut_nasional',
        'status_imut_prioritas',
        'status_imut_skp',
        'status_imut_unit',
        'status_imunas_2021',
        'tampil_survey',
        'kategori',
        'urutan',
        'type_persen',
        'imut_must_valid',
        'files',
    ];

    // Relasi yang sudah kita buat sebelumnya
    public function variables()
    {
        return $this->hasMany(HospitalSurveyIndicatorVariable::class, 'variable_indicator_id', 'indicator_id');
    }

    /**
     * ======================================
     * ## PERBAIKAN UTAMA ADA DI SINI ##
     * ======================================
     * Ubah nama fungsi menjadi jamak (plural) -> departemens()
     */
    public function departemens()
    {
        return $this->belongsToMany(Departemen::class, 'mapping_indikator_unit', 'id_indikator', 'id_unit');
    }

    public function statuses()
    {
        return $this->belongsToMany(StatusKategori::class, 'indikator_status', 'indikator_id', 'status_id');
    }

    /**
     * Relasi ke ImutCategory.
     */
    public function imutCategory()
    {
        return $this->belongsTo(ImutCategory::class, 'indicator_category_id', 'imut_category_id');
    }

    /**
     * ✅ TAMBAHKAN INI - Relasi ke HospitalSurveyIndicatorResult
     */
    public function results()
    {
        return $this->hasMany(HospitalSurveyIndicatorResult::class, 'result_indicator_id', 'indicator_id');
    }
}
