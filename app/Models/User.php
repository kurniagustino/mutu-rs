<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 👈 1. UBAH/TAMBAHKAN INI (dari Departemen ke Ruangan)
use Spatie\Permission\Traits\HasRoles; // 👈 2. TAMBAHKAN INI

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
    protected $with = ['ruangans', 'roles']; // 👈 3. GANTI NAMA ('departemens' -> 'ruangans')

    /**
     * ✅ RELASI BARU: User bisa punya banyak ruangan via pivot
     */
    public function ruangans(): BelongsToMany
    {
        return $this->belongsToMany(
            Ruangan::class,            // 👈 4. GANTI MODEL ('Departemen::class' -> 'Ruangan::class')
            'mapping_pengguna_unit',   // Pivot table
            'user_id',                 // FK di pivot untuk User
            'id_ruang'                 // FK di pivot untuk Ruangan
        )
            ->withPivot('level')       // Include column 'level' dari pivot
            ->withTimestamps();        // Include created_at & updated_at dari pivot
    }

    /**
     * ✅ ACCESSOR BARU: Ruangan utama (first ruangan)
     * Menggantikan getDepartemenAttribute
     */
    public function getRuanganUtamaAttribute()
    {
        // 👈 5. GANTI RELASI ('$this->departemens' -> '$this->ruangans')
        return $this->ruangans->first();
    }

    /**
     * ✅ ACCESSOR: ID Ruang utama (untuk backward compatibility)
     * TETAP DIPERTAHANKAN, HANYA GANTI LOGIKA
     */
    public function getIdRuangAttribute()
    {
        // 👈 6. GANTI RELASI ('$this->departemens' -> '$this->ruangans')
        return $this->ruangans->first()?->id_ruang;
    }

    /**
     * ✅ ACCESSOR: Level utama (dari pivot)
     * TETAP DIPERTAHANKAN, HANYA GANTI LOGIKA
     */
    public function getLevelAttribute()
    {
        // 👈 7. GANTI RELASI ('$this->departemens' -> '$this->ruangans')
        return $this->ruangans->first()?->pivot->level;
    }

    // --- SISA FUNGSI DI BAWAH INI TIDAK BERUBAH ---

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
