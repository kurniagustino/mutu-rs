<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
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
            'password' => 'hashed',
        ];
    }

    // ✅ EAGER LOAD departemen (prevent N+1 queries)
    protected $with = ['departemen'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_ruang', 'id_ruang');
    }

    public function getFilamentName(): string
    {
        return $this->getAttribute('name') ?? '';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
