<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Tarif, Lokasi, MetodePembayaran};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParkController extends Controller
{
    public function dashboard()
    {
        $user    = Auth::user();
        $petugas = $user->petugas;

        if (!$petugas) {
            return redirect()->route('login')->withErrors(['username' => 'Data petugas tidak ditemukan.']);
        }

        $lokasiIds  = is_array($petugas->id_lokasi) ? $petugas->id_lokasi : [];
        $lokasi     = count($lokasiIds) ? Lokasi::whereIn('id', $lokasiIds)->get() : collect();
        $today      = Carbon::today();

        $stats = [
            'transaksi_hari_ini' => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->count(),
            'kendaraan_parkir'   => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->where('status', 0)->count(),
            'pendapatan_hari_ini' => (float) Transaksi::where('id_petugas', $petugas->id)
                                        ->whereDate('tgl', $today)->where('status', 1)
                                        ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                        ->sum('tarif.tarif'),
        ];

        $aktivParkir = Transaksi::with(['tarif', 'lokasi'])
                        ->where('id_petugas', $petugas->id)
                        ->whereDate('tgl', $today)
                        ->where('status', 0)
                        ->latest()->limit(10)->get();

        return view('petugas.dashboard.index', compact('petugas', 'lokasi', 'stats', 'aktivParkir'));
    }

    public function formTambah()
    {
        $petugas    = Auth::user()->petugas;
        $lokasiIds  = is_array($petugas->id_lokasi) ? $petugas->id_lokasi : [];
        $lokasi     = count($lokasiIds) ? Lokasi::whereIn('id', $lokasiIds)->get() : collect();
        $tarif      = Tarif::all();
        return view('petugas.dashboard.tambah', compact('lokasi', 'tarif', 'petugas'));
    }

    public function simpanMasuk(Request $request)
    {
        $request->validate([
            'nomor_polisi' => 'required|string|max:15',
            'id_tarif'     => 'required|exists:tarif,id',
            'id_lokasi'    => 'required|exists:lokasi,id',
        ]);

        $petugas = Auth::user()->petugas;

        Transaksi::create([
            'nomor_referensi'      => Transaksi::generateNomorReferensi(),
            'id_petugas'           => $petugas->id,
            'id_lokasi'            => $request->id_lokasi,
            'tgl'                  => Carbon::today(),
            'id_tarif'             => $request->id_tarif,
            'nomor_polisi'         => strtoupper(trim($request->nomor_polisi)),
            'jam_masuk'            => Carbon::now()->format('H:i:s'),
            'status'               => 0,
        ]);

        return redirect()->route('petugas.dashboard')->with('success', 'Kendaraan berhasil masuk!');
    }

    public function riwayat(Request $request)
    {
        $petugas = Auth::user()->petugas;

        $query = Transaksi::with(['tarif', 'lokasi', 'metodePembayaran'])
                    ->where('id_petugas', $petugas->id);

        if ($request->filled('tgl')) {
            $query->whereDate('tgl', $request->tgl);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('nomor_polisi')) {
            $query->where('nomor_polisi', 'like', '%' . $request->nomor_polisi . '%');
        }

        $transaksis = $query->latest()->paginate(15)->withQueryString();

        $totalTrx   = $query->toBase()->getCountForPagination();
        $totalLunas = Transaksi::where('id_petugas', $petugas->id)
                        ->when($request->filled('tgl'), fn($q) => $q->whereDate('tgl', $request->tgl))
                        ->where('status', 1)->count();
        $totalBelum = Transaksi::where('id_petugas', $petugas->id)
                        ->when($request->filled('tgl'), fn($q) => $q->whereDate('tgl', $request->tgl))
                        ->where('status', 0)->count();

        return view('petugas.riwayat.index', compact('transaksis', 'totalTrx', 'totalLunas', 'totalBelum'));
    }

    public function scanCheckout()
    {
        return view('petugas.dashboard.scan');
    }

    public function cariCheckout(Request $request)
    {
        $petugas      = Auth::user()->petugas;
        $nomor_polisi = strtoupper(trim($request->nomor_polisi ?? $request->q ?? ''));

        $transaksi = Transaksi::with(['tarif', 'lokasi'])
                    ->where('id_petugas', $petugas->id)
                    ->where('status', 0)
                    ->where('nomor_polisi', 'like', '%' . $nomor_polisi . '%')
                    ->latest()->first();

        if (!$transaksi) {
            return response()->json(['found' => false]);
        }

        $jamMasuk = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $durasi   = $jamMasuk->diffForHumans(now(), true);

        $metode = MetodePembayaran::all();

        return response()->json([
            'found' => true,
            'transaksi' => [
                'id'             => $transaksi->id,
                'nomor_referensi'=> $transaksi->nomor_referensi,
                'nomor_polisi'   => $transaksi->nomor_polisi,
                'lokasi'         => $transaksi->lokasi->nama ?? '-',
                'tarif_nama'     => $transaksi->tarif->nama ?? '-',
                'tarif_harga'    => $transaksi->tarif->tarif ?? 0,
                'jam_masuk'      => $transaksi->jam_masuk,
                'durasi'         => $durasi,
                'status'         => $transaksi->status,
                'jam_keluar'     => $transaksi->jam_keluar,
            ],
            'metode' => $metode,
        ]);
    }

    public function detailCheckout(Request $request, Transaksi $transaksi)
    {
        $transaksi->load(['tarif', 'lokasi', 'metodePembayaran']);

        if ($request->wantsJson()) {
            $jamMasuk = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
            $durasi   = $transaksi->jam_keluar
                ? Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk)
                         ->diffForHumans(Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_keluar), true)
                : $jamMasuk->diffForHumans(now(), true);

            return response()->json([
                'transaksi' => [
                    'id'             => $transaksi->id,
                    'nomor_referensi'=> $transaksi->nomor_referensi,
                    'nomor_polisi'   => $transaksi->nomor_polisi,
                    'lokasi'         => $transaksi->lokasi->nama ?? '-',
                    'tarif_nama'     => $transaksi->tarif->nama ?? '-',
                    'tarif_harga'    => $transaksi->tarif->tarif ?? 0,
                    'jam_masuk'      => $transaksi->jam_masuk,
                    'jam_keluar'     => $transaksi->jam_keluar,
                    'durasi'         => $durasi,
                    'status'         => $transaksi->status,
                ],
            ]);
        }

        $metode = MetodePembayaran::all();
        return view('petugas.dashboard.checkout', compact('transaksi', 'metode'));
    }

    public function prosesCheckout(Request $request, Transaksi $transaksi)
    {
        $request->validate(['id_metode_pembayaran' => 'required|exists:metode_pembayaran,id']);

        $jamMasuk  = Carbon::parse($transaksi->tgl->format('Y-m-d') . ' ' . $transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $durasi    = $jamMasuk->diffForHumans($jamKeluar, true);

        $transaksi->update([
            'jam_keluar'           => $jamKeluar->format('H:i:s'),
            'id_metode_pembayaran' => $request->id_metode_pembayaran,
            'status'               => 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'message'          => 'Checkout berhasil',
                'nomor_referensi'  => $transaksi->nomor_referensi,
                'durasi'           => $durasi,
                'total'            => $transaksi->tarif->tarif ?? 0,
            ]);
        }

        return redirect()->route('petugas.dashboard')
            ->with('success', 'Checkout berhasil! Kendaraan ' . $transaksi->nomor_polisi . ' telah keluar.');
    }

    public function profile()
    {
        $user          = Auth::user();
        $petugas       = $user->petugas;
        $lokasiIds     = is_array($petugas->id_lokasi) ? $petugas->id_lokasi : [];
        $lokasiPetugas = count($lokasiIds) ? Lokasi::whereIn('id', $lokasiIds)->get() : collect();
        $today         = Carbon::today();

        $statHariIni = [
            'total'  => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->count(),
            'lunas'  => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->where('status', 1)->count(),
            'parkir' => Transaksi::where('id_petugas', $petugas->id)->whereDate('tgl', $today)->where('status', 0)->count(),
        ];

        return view('petugas.profile.index', compact('petugas', 'lokasiPetugas', 'statHariIni'));
    }
}