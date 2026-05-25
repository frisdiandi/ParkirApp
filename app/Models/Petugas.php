<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';

    protected $fillable = [
        'id_user', 'nomor_rekening', 'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function lokasi()
    {
        return $this->belongsToMany(Lokasi::class, 'petugas_lokasi', 'petugas_id', 'lokasi_id')
                    ->withTimestamps();
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_petugas');
    }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : asset('images/default-avatar.png');
    }
}
