<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ruangan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'ruangan';

    /**
     * Primary Key kustom.
     * Sesuai migration, kita pakai 'id_ruang'.
     */
    protected $primaryKey = 'id_ruang';

    /**
     * Tipe datanya adalah integer (bawaan) dan
     * auto-increment (bawaan dari $table->id('id_ruang')).
     *
     * JANGAN pakai 'incrementing = false' atau 'keyType = string'
     * dari model lama, karena itu SALAH. Migration kita sudah benar.
     */

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'id_unit', // Penting untuk relasi
        'nama_ruang',
        'sink',
    ];

    // ==================================================================
    // RELASI BARU
    // ==================================================================

    /**
     * Relasi ke Unit (Satu Ruangan dimiliki oleh Satu Unit).
     */
    public function unit(): BelongsTo
    {
        // 'id_unit' adalah foreign key di tabel INI (ruangan)
        // 'id' adalah primary key di tabel TUJUAN (unit)
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
    }

    // ==================================================================
    // RELASI DARI MODEL LAMA YANG DIPINDAH
    // ==================================================================

    /**
     * Relasi ke Users (many-to-many).
     * Ini kita pindah dari model Departemen lama, karena relasinya
     * pakai 'id_ruang', yang sekarang jadi tanggung jawab model Ruangan.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'mapping_pengguna_unit',
            'id_ruang', // Kunci dari model INI (Ruangan)
            'user_id'   // Kunci dari model TUJUAN (User)
        )
            ->withPivot('level')
            ->withTimestamps();
    }
}
