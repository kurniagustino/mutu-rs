<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ✅ TAMBAHKAN USE STATEMENT INI
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Tambahkan juga model User jika belum ada

class HospitalSurveyIndicator extends Model
{
    use HasFactory;

    protected $table = 'hospital_survey_indicator';

    protected $primaryKey = 'indicator_id';

    // Eager load otomatis untuk relasi yang sering dipakai
    // 'user' adalah opsional, bisa ditambahkan jika selalu ingin load PIC
    protected $with = ['imutCategory'];

    // public $timestamps = false; // ❌ KITA HAPUS, KARENA MIGRASI BARU PAKAI timestamps()

    /**
     * ✅ PERBAIKAN $fillable AGAR SESUAI MIGRASI BARU
     * Kolom yang boleh diisi dari form.
     */
    protected $fillable = [
        'indicator_name',
        'dimensi_mutu',
        'tujuan',
        'satuan_pengukuran',
        'indicator_definition',
        'indicator_criteria_inclusive',
        'indicator_criteria_exclusive',
        'indicator_source_of_data',
        'indicator_type',
        'indicator_monitoring_area',
        'indicator_frequency',
        'indicator_target',
        'urutan',
        'indicator_category_id',
        'penanggung_jawab_id',
        'indicator_record_status',
        'files',
    ];

    // Relasi ke Variabel (Nemu/Demu)
    public function variables(): HasMany
    {
        return $this->hasMany(HospitalSurveyIndicatorVariable::class, 'variable_indicator_id', 'indicator_id');
    }

    // Relasi ke Unit (jika masih dipakai)
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'mapping_indikator_unit', 'id_indikator', 'id_unit');
    }

    // Relasi ke Status Kategori (jika masih dipakai)
    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(StatusKategori::class, 'indikator_status', 'indikator_id', 'status_id');
    }

    // Relasi ke ImutCategory (Kategori Area)
    public function imutCategory(): BelongsTo
    {
        return $this->belongsTo(ImutCategory::class, 'indicator_category_id', 'imut_category_id');
    }

    // Relasi ke Hasil (jika masih dipakai)
    public function results(): HasMany
    {
        return $this->hasMany(HospitalSurveyIndicatorResult::class, 'result_indicator_id', 'indicator_id');
    }

    /**
     * ✅ INI DIA RELASI YANG HILANG
     * Relasi ke User sebagai Penanggung Jawab (PIC)
     */
    public function user(): BelongsTo
    {
        // 'penanggung_jawab_id' adalah foreign key di tabel hospital_survey_indicator
        // 'id' adalah primary key di tabel users
        return $this->belongsTo(User::class, 'penanggung_jawab_id', 'id');
    }
}
