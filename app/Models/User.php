<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'nama', 'password', 'contact', 'level', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'level' => 'integer',
        'status' => 'integer',
    ];

    public function petugas()
    {
        return $this->hasOne(Petugas::class, 'id_user');
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            1 => 'Admin',
            2 => 'Petugas',
            3 => 'Pimpinan',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 1 ? 'Aktif' : 'Tidak Aktif';
    }

    public function isAdmin(): bool    { return $this->level === 1; }
    public function isPetugas(): bool  { return $this->level === 2; }
    public function isPimpinan(): bool { return $this->level === 3; }
    public function isAktif(): bool    { return $this->status === 1; }
}
