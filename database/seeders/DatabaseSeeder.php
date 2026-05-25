<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lokasi;
use App\Models\Tarif;
use App\Models\MetodePembayaran;
use App\Models\Petugas;
use App\Models\Transaksi;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────────────
        User::create([
            'username' => 'admin',
            'nama'     => 'Administrator',
            'password' => Hash::make('admin123'),
            'contact'  => '081234567890',
            'level'    => 1,
            'status'   => 1,
        ]);

        User::create([
            'username' => 'pimpinan',
            'nama'     => 'Kepala Dinas',
            'password' => Hash::make('pimpinan123'),
            'contact'  => '081234567891',
            'level'    => 3,
            'status'   => 1,
        ]);

        $userPetugas1 = User::create([
            'username' => 'petugas01',
            'nama'     => 'Budi Santoso',
            'password' => Hash::make('petugas123'),
            'contact'  => '081234567892',
            'level'    => 2,
            'status'   => 1,
        ]);

        $userPetugas2 = User::create([
            'username' => 'petugas02',
            'nama'     => 'Andi Pratama',
            'password' => Hash::make('petugas123'),
            'contact'  => '081234567893',
            'level'    => 2,
            'status'   => 1,
        ]);

        $userPetugas3 = User::create([
            'username' => 'petugas03',
            'nama'     => 'Siti Rahmawati',
            'password' => Hash::make('petugas123'),
            'contact'  => '081234567894',
            'level'    => 2,
            'status'   => 1,
        ]);

        // ── Lokasi Parkir (area Padang Panjang) ──────────────────────────────
        $lokasiData = [
            ['nama' => 'Parkir Pasar Pusat Padang Panjang',  'koordinat' => '-0.4639,100.4035'],
            ['nama' => 'Parkir Terminal Bukit Surungan',     'koordinat' => '-0.4655,100.4012'],
            ['nama' => 'Parkir Masjid Asasi Sigando',        'koordinat' => '-0.4598,100.4078'],
            ['nama' => 'Parkir Stasiun Padang Panjang',      'koordinat' => '-0.4623,100.4025'],
            ['nama' => 'Parkir Kuliner Pulau Air',           'koordinat' => '-0.4581,100.4112'],
            ['nama' => 'Parkir RSUD Padang Panjang',         'koordinat' => '-0.4567,100.4145'],
            ['nama' => 'Parkir Lapangan Bancah Laweh',       'koordinat' => '-0.4612,100.4067'],
            ['nama' => 'Parkir ISI Padang Panjang',          'koordinat' => '-0.4534,100.4198'],
            ['nama' => 'Parkir Mifan Waterpark',             'koordinat' => '-0.4789,100.3923'],
            ['nama' => 'Parkir Lubuk Mata Kucing',           'koordinat' => '-0.4456,100.4256'],
        ];
        $lokasi = collect($lokasiData)->map(fn($l) => Lokasi::create($l));

        // ── Tarif ────────────────────────────────────────────────────────────
        Tarif::create(['nama' => 'Roda 2 (Motor)',    'tarif' => 2000]);
        Tarif::create(['nama' => 'Roda 4 (Mobil)',    'tarif' => 5000]);
        Tarif::create(['nama' => 'Roda 6 (Truk/Bus)', 'tarif' => 10000]);

        // ── Metode Pembayaran ────────────────────────────────────────────────
        MetodePembayaran::create(['nama' => 'Cash']);
        MetodePembayaran::create(['nama' => 'QRIS']);

        // ── Petugas ──────────────────────────────────────────────────────────
        $petugas1 = Petugas::create([
            'id_user'        => $userPetugas1->id,
            'nomor_rekening' => '1234567890',
            'foto'           => null,
        ]);
        $petugas1->lokasi()->attach([$lokasi[0]->id, $lokasi[1]->id, $lokasi[2]->id]);

        $petugas2 = Petugas::create([
            'id_user'        => $userPetugas2->id,
            'nomor_rekening' => '2345678901',
            'foto'           => null,
        ]);
        $petugas2->lokasi()->attach([$lokasi[3]->id, $lokasi[4]->id]);

        $petugas3 = Petugas::create([
            'id_user'        => $userPetugas3->id,
            'nomor_rekening' => '3456789012',
            'foto'           => null,
        ]);
        $petugas3->lokasi()->attach([$lokasi[5]->id, $lokasi[6]->id, $lokasi[7]->id]);

        // ── Transaksi dummy untuk demo riwayat ───────────────────────────────
        $outletId = config('services.bank_nagari.outlet_id', '007210024');
        $pjsp     = config('services.bank_nagari.pjsp', 'NGR');

        $samples = [
            // [plat, idx_lokasi, id_tarif, id_metode, jam_masuk, jam_keluar]
            ['BA 1234 AB', 0, 1, 1, '08:15:00', '10:30:00'],
            ['BA 5678 CD', 1, 2, 1, '09:00:00', '11:45:00'],
            ['BA 9012 EF', 0, 1, 2, '10:20:00', null],
            ['B 3456 GH',  2, 3, 1, '07:30:00', '15:20:00'],
            ['BM 7890 IJ', 1, 1, 1, '08:45:00', '12:00:00'],
            ['BA 1357 KL', 2, 2, 2, '11:00:00', null],
            ['BA 2468 MN', 0, 1, 2, '12:30:00', null],
        ];

        foreach ($samples as [$plat, $lokIdx, $tarifId, $metodeId, $jamMasuk, $jamKeluar]) {
            $status = $jamKeluar ? 1 : 0;
            $tarifVal = $tarifId == 1 ? 2000 : ($tarifId == 2 ? 5000 : 10000);

            Transaksi::create([
                'reference_number'     => Transaksi::generateNomorReferensi(),
                'billing_number'       => Transaksi::generateBillingNumber(),
                'id_petugas'           => $petugas1->id,
                'id_lokasi'            => $lokasi[$lokIdx]->id,
                'tgl'                  => Carbon::today()->subDays(rand(0, 3)),
                'id_tarif'             => $tarifId,
                'nomor_polisi'         => $plat,
                'jam_masuk'            => $jamMasuk,
                'jam_keluar'           => $jamKeluar,
                'id_metode_pembayaran' => $status === 1 ? $metodeId : null,
                'amount'               => $status === 1 ? (string) $tarifVal : null,
                'outlet_id'            => $status === 1 ? $outletId : null,
                'pjsp'                 => $status === 1 ? $pjsp : null,
                'customer_name'        => $status === 1 ? 'CUSTOMER-' . strtoupper(substr(md5($plat), 0, 6)) : null,
                'status'               => $status,
            ]);
        }
    }
}
