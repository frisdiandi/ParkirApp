<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    protected $fillable = ['nama', 'koordinat', 'foto'];

    public function getFotoUrlAttribute(): string
    {
        return $this->foto ? asset('storage/' . $this->foto) : asset('images/default-lokasi.png');
    }
}
