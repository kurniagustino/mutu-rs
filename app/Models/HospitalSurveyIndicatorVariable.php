<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HospitalSurveyIndicatorVariable extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama tabel yang benar
    protected $table = 'hospital_survey_indicator_variable';

    // Beri tahu Laravel nama primary key-nya
    protected $primaryKey = 'variable_id';
    
    // Beri tahu Laravel bahwa tabel ini tidak punya kolom created_at dan updated_at
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     * Kolom ini ditambahkan agar form bisa menyimpan data.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'variable_indicator_id', // Foreign key dari tabel indicator
        'variable_name',
        'variable_type',
        'variable_description',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model HospitalSurveyIndicator.
     * Ini adalah kebalikan dari relasi "hasMany" di model satunya.
     */
    public function indicator(): BelongsTo
    {
        // Sesuaikan foreign key dan owner key jika berbeda
        return $this->belongsTo(HospitalSurveyIndicator::class, 'variable_indicator_id', 'indicator_id');
    }
}

