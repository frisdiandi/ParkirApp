<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\Tarif;
use App\Models\MetodePembayaran;
use App\Models\Petugas;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $admin = User::create([
            'username' => 'admin',
            'nama'     => 'Administrator',
            'password' => Hash::make('admin123'),
            'contact'  => '081234567890',
            'level'    => 1,
            'status'   => 1,
        ]);

        $pimpinan = User::create([
            'username' => 'pimpinan',
            'nama'     => 'Kepala Dinas',
            'password' => Hash::make('pimpinan123'),
            'contact'  => '081234567891',
            'level'    => 3,
            'status'   => 1,
        ]);

        $userPetugas = User::create([
            'username' => 'petugas01',
            'nama'     => 'Budi Santoso',
            'password' => Hash::make('petugas123'),
            'contact'  => '081234567892',
            'level'    => 2,
            'status'   => 1,
        ]);

        // Lokasi
        $lokasi1 = Lokasi::create(['nama' => 'Parkir Pasar Atas', 'koordinat' => '-3.0123,104.7456']);
        $lokasi2 = Lokasi::create(['nama' => 'Parkir Terminal Karya Jaya', 'koordinat' => '-3.0234,104.7567']);
        $lokasi3 = Lokasi::create(['nama' => 'Parkir Masjid Agung', 'koordinat' => '-3.0345,104.7678']);

        // Tarif
        Tarif::create(['nama' => 'Roda 2 (Motor)', 'tarif' => 2000]);
        Tarif::create(['nama' => 'Roda 4 (Mobil)', 'tarif' => 5000]);
        Tarif::create(['nama' => 'Roda 6 (Truk/Bus)', 'tarif' => 10000]);

        // Metode Pembayaran
        MetodePembayaran::create(['nama' => 'Cash']);
        MetodePembayaran::create(['nama' => 'QRIS']);

        // Petugas
        Petugas::create([
            'id_user'        => $userPetugas->id,
            'id_lokasi'      => json_encode([$lokasi1->id, $lokasi2->id]),
            'nomor_rekening' => '1234567890',
            'foto'           => null,
        ]);
    }
}
