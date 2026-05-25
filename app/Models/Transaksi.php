<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $fillable = [
        'id_petugas',
        'id_lokasi',
        'tgl',
        'id_tarif',
        'nomor_polisi',
        'jam_masuk',
        'jam_keluar',
        'id_metode_pembayaran',
        'status',
        'outlet_id',
        'billing_number',
        'amount',
        'reference_number',
        'pjsp',
        'customer_name',
    ];

    protected $appends = ['nomor_referensi'];

    protected $casts = [
        'tgl'    => 'date',
        'status' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif');
    }

    public function metode()
    {
        return $this->belongsTo(MetodePembayaran::class, 'id_metode_pembayaran');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'id_metode_pembayaran');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 1 ? 'Lunas' : 'Belum Bayar';
    }

    public function getNomorReferensiAttribute(): ?string
    {
        return $this->reference_number;
    }

    public function getDurasiAttribute(): string
    {
        if (!$this->jam_keluar) return '-';

        $masuk  = \Carbon\Carbon::parse($this->jam_masuk);
        $keluar = \Carbon\Carbon::parse($this->jam_keluar);
        $diff   = $masuk->diff($keluar);

        return $diff->h . 'j ' . $diff->i . 'm';
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function generateNomorReferensi(): string
    {
        do {
            $kode = 'PRK-' . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (static::where('reference_number', $kode)->exists());

        return $kode;
    }

    public static function generateBillingNumber(): string
    {
        return 'BIL-' . strtoupper(substr(md5(uniqid('', true)), 0, 10));
    }
}