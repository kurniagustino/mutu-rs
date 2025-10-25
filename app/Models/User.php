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

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'NIP',
        'id_ruang',
        'level',
    ];

    protected $hidden = ['password'];

    // ✅ OPTIMASI: Tambah eager loading untuk mencegah N+1
    protected $with = ['departemen'];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_ruang', 'id_ruang');
    }

    public function getFilamentName(): string
    {
        return $this->getAttribute('name') ?? ''; // <-- UBAH DI SINI
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
