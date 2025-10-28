<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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

    // ✅ Eager load departemens & roles
    protected $with = ['departemens', 'roles'];

    /**
     * ✅ MANY-TO-MANY: User bisa punya banyak departemen via pivot
     */
    public function departemens()
    {
        return $this->belongsToMany(
            \App\Models\Departemen::class,
            'mapping_pengguna_unit',  // Pivot table
            'user_id',                 // FK di pivot untuk User
            'id_ruang',                // FK di pivot untuk Departemen
            'id',                      // PK di table users
            'id_ruang'                 // PK di table departemen
        )
            ->withPivot('level')           // Include column 'level' dari pivot
            ->withTimestamps();            // Include created_at & updated_at dari pivot
    }

    /**
     * ✅ ACCESSOR: Departemen utama (first departemen)
     */
    public function getDepartemenAttribute()
    {
        return $this->departemens->first();
    }

    /**
     * ✅ ACCESSOR: ID Ruang utama (untuk backward compatibility)
     */
    public function getIdRuangAttribute()
    {
        return $this->departemens->first()?->id_ruang;
    }

    /**
     * ✅ ACCESSOR: Level utama (dari pivot)
     */
    public function getLevelAttribute()
    {
        return $this->departemens->first()?->pivot->level;
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
