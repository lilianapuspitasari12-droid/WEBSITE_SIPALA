<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'nomor_hp',
        'email',
        'password',
        'role',
        'kelas_jabatan',
        'foto_profil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ROLE = [
        'siswa' => 'Siswa',
        'guru' => 'Guru / Staf',
        'admin' => 'Petugas',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        return self::ROLE[$this->role] ?? $this->role;
    }
}