<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasPanelShield, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'NIP',
        'level',
        'id_ruang',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }

    // ✅ Eager load untuk performa
    protected $with = ['departemen', 'roles'];

    public function departemen()
    {
        return $this->belongsTo(\App\Models\Departemen::class, 'id_ruang', 'id_ruang');
    }

    public function getFilamentName(): string
    {
        return $this->name ?? '';
    }

    /**
     * ✅ 100% DYNAMIC - tidak hardcode role apapun
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();

        // return true;
    }

    /**
     * ✅ 100% DYNAMIC - auto-format role name tanpa hardcode
     */
    public function getRoleDisplayAttribute(): string
    {
        $role = $this->roles->first();

        if ($role) {
            // Auto-format: snake_case → Title Case
            return ucwords(str_replace('_', ' ', $role->name));
        }

        return 'No Role';
    }
}
