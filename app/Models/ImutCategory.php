<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImutCategory extends Model
{
    use HasFactory;

    protected $table = 'imut_category';

    protected $primaryKey = 'imut_category_id';

    public $timestamps = false;

    // ✅ PERBAIKAN: Sesuaikan fillable
    protected $fillable = ['imut_name_category'];

    // ✅ PERBAIKAN: Ganti accessor ke kolom baru
    public function getCategoryNameAttribute()
    {
        return $this->imut_name_category;
    }

    public function indicators()
    {
        return $this->hasMany(HospitalSurveyIndicator::class, 'indicator_category_id', 'imut_category_id');
    }
}
