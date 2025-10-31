<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// 👈 TAMBAHKAN INI

class Unit extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * Sesuai migration yang kita buat tadi.
     */
    protected $table = 'unit';

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'nama_unit',
    ];

    // Kita pakai Primary Key default 'id' (auto-increment, integer),
    // jadi tidak perlu setting apa-apa lagi.

    // ==================================================================
    // RELASI BARU
    // ==================================================================

    /**
     * Relasi ke Ruangan (Satu Unit punya Banyak Ruangan).
     */
    public function ruangan(): HasMany
    {
        // 'id_unit' adalah foreign key di tabel 'ruangan'
        // 'id' adalah primary key di tabel INI (unit)
        return $this->hasMany(Ruangan::class, 'id_unit', 'id');
    }

    // ==================================================================
    // RELASI DARI MODEL LAMA YANG DIPINDAH
    // ==================================================================

    /**
     * Relasi ke Indikator (many-to-many).
     * Ini kita pindah dari model Departemen lama, karena relasinya
     * pakai 'id_unit', yang sekarang jadi tanggung jawab model Unit.
     */
    public function indicators(): BelongsToMany // 👈 INI BENAR (plural)
    {
        return $this->belongsToMany(
            HospitalSurveyIndicator::class,
            'mapping_indikator_unit',
            'id_unit',
            'id_indikator'
        );
    }
}
