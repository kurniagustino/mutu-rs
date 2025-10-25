<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImutCategory extends Model
{
    use HasFactory;

    // Mendefinisikan nama tabel secara eksplisit
    protected $table = 'imut_category';

    // Mendefinisikan primary key secara eksplisit
    protected $primaryKey = 'imut_category_id';

    // Menonaktifkan timestamps (created_at & updated_at)
    public $timestamps = false;

    // Mendefinisikan kolom yang boleh diisi
    protected $fillable = ['imut'];

    // ✅ TAMBAHKAN INI
    public function getCategoryNameAttribute()
    {
        return $this->imut;
    }

    /**
     * ✅ TAMBAHKAN RELATIONSHIP KE HospitalSurveyIndicator
     */
    public function indicators()
    {
        return $this->hasMany(HospitalSurveyIndicator::class, 'indicator_category_id', 'imut_category_id');
    }
}
