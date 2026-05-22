<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';

    protected $fillable = [
        'id_user', 'id_lokasi', 'nomor_rekening', 'foto',
    ];

    protected $casts = [
        'id_lokasi' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function lokasi()
    {
        $ids = is_array($this->id_lokasi) ? $this->id_lokasi : [];
        return count($ids) ? Lokasi::whereIn('id', $ids)->get() : collect();
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