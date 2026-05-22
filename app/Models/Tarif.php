<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $table = 'tarif';
    protected $fillable = ['nama', 'tarif', 'foto'];

    protected $casts = ['tarif' => 'decimal:2'];

    public function getFotoUrlAttribute(): string
    {
        return $this->foto ? asset('storage/' . $this->foto) : asset('images/default-tarif.png');
    }

    public function getTarifFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->tarif, 0, ',', '.');
    }
}
