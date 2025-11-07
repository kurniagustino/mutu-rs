<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// Catatan Review:
// - Import use statement untuk Ruangan, Unit, dan MappingPenggunaUnit belum ada. Jika belum ada, harus ditambahkan.
// - Penggunaan 'hasManyThrough' di function units() tampak keliru karena tidak sesuai sintaks/relasi Eloquent: hasManyThrough tidak dipakai lewat pivot table. Perlu dicek lagi atau gunakan relasi belongsToMany ke Unit lewat Ruangan, atau relasi langsung melalui Ruangan jika Unit ada hubungannya.
// - Kode lain secara umum benar asalkan model/relasi lain (Ruangan, MappingPenggunaUnit) sudah sesuai. Pastikan Ruangan, Unit, dan MappingPenggunaUnit sudah ada.
// - Di getter getIdRuangAttribute, properti 'id_ruang' kemungkinan tidak ada di model Ruangan kecuali memang field itu adalah PK di Ruangan. Biasanya laravel expects 'id' sebagai PK. Jika id_ruang adalah PK Ruangan, oke; jika tidak, kemungkinan typo dan harusnya 'id'.
// - Eager load 'roles' akan double-load (karena HasRoles sudah eager-load roles). Tidak error, tapi redundant.
// - Jika mapping_pengguna_unit pakai custom Pivot Model, pastikan MappingPenggunaUnit extend Pivot.
// - Tidak ada error sintaks fatal, namun cek definisi model, field PK, dan relasi.

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'NIP',
        'identitas',
        'aktivasi',
        'status',
        'fto',
        'tgllahir',
        'tempatlahir',
        'glr_depan',
        'glr_blkg',
        'alamat',
        'pendidikan_terakhir',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tgllahir' => 'date',
        ];
    }

    // ✅ Eager load ruangans & roles
    protected $with = ['ruangans', 'roles']; // Jika sudah ada di HasRoles, double loading roles

    /**
     * ✅ RELASI BARU: User bisa punya banyak ruangan via pivot
     */
    public function ruangans(): BelongsToMany
    {
        return $this->belongsToMany(
            Ruangan::class,
            'mapping_pengguna_unit',
            'user_id',
            'id_ruang'
        )
            ->withPivot('level')
            ->withTimestamps()
            ->using(MappingPenggunaUnit::class);
    }

    // Warning: hasManyThrough tidak lazim untuk kasus pivot many to many.
    public function units()
    {
        // Ini kemungkinan salah penggunaan hasManyThrough. Tidak error sintaks, tapi hasilnya belum tentu sesuai harapan.
        return $this->hasManyThrough(
            Unit::class,
            Ruangan::class,
            'id_ruang',  // <--- cek, biasanya foreign key ke Unit, bukan ke Ruangan!
            'mapping_pengguna_unit', // <--- ini nama tabel, padahal argumen keempat di hasManyThrough adalah foreign key di target table; harusnya string field di Unit, bukan nama tabel!
            'user_id',   // <--- ini foreign key di User (relasi pertama), harusnya primary key User
            'id_unit'    // <--- ini local key pada Ruangan; harus field yang benar-benar ada
        );
    }

    /**
     * ✅ ACCESSOR BARU: Ruangan utama (first ruangan)
     */
    public function getRuanganUtamaAttribute()
    {
        return $this->ruangans->first();
    }

    /**
     * ✅ ACCESSOR: ID Ruang utama
     */
    public function getIdRuangAttribute()
    {
        // Catatan: jika id_ruang adalah PK di Ruangan, ini benar; jika field lain (biasa PK = 'id'), harusnya $this->ruangans->first()?->id
        return $this->ruangans->first()?->id_ruang;
    }

    /**
     * ✅ ACCESSOR: Level utama (dari pivot)
     */
    public function getLevelAttribute()
    {
        return $this->ruangans->first()?->pivot->level;
    }

    public function getFilamentName(): string
    {
        return $this->name ?? '';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }

    public function getRoleDisplayAttribute(): string
    {
        $role = $this->roles->first();

        if ($role) {
            return ucwords(str_replace('_', ' ', $role->name));
        }

        return 'No Role';
    }
}
