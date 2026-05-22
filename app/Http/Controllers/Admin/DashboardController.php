<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Petugas, Lokasi, User};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear  = Carbon::now()->year;

        $stats = [
            'transaksi_hari_ini'  => Transaksi::whereDate('tgl', $today)->count(),
            'transaksi_bulan_ini' => Transaksi::whereMonth('tgl', $thisMonth)->whereYear('tgl', $thisYear)->count(),
            'pendapatan_hari_ini' => Transaksi::whereDate('tgl', $today)->where('status', 1)
                                        ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                        ->sum('tarif.tarif'),
            'pendapatan_bulan_ini' => Transaksi::whereMonth('tgl', $thisMonth)->whereYear('tgl', $thisYear)
                                        ->where('status', 1)
                                        ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                        ->sum('tarif.tarif'),
            'total_petugas'       => User::where('level', 2)->where('status', 1)->count(),
            'total_lokasi'        => Lokasi::count(),
            'kendaraan_parkir'    => Transaksi::whereDate('tgl', $today)->where('status', 0)->count(),
        ];

        // Grafik 7 hari terakhir
        $grafikData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $grafikData[] = [
                'tgl'        => $tgl->format('d M'),
                'transaksi'  => Transaksi::whereDate('tgl', $tgl)->count(),
                'pendapatan' => (float) Transaksi::whereDate('tgl', $tgl)->where('status', 1)
                                    ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                                    ->sum('tarif.tarif'),
            ];
        }

        // Transaksi terbaru
        $transaksiTerbaru = Transaksi::with(['petugas.user', 'lokasi', 'tarif'])
                            ->latest()->limit(10)->get();

        // Per lokasi hari ini
        $perLokasi = Transaksi::whereDate('tgl', $today)
                        ->select('id_lokasi', DB::raw('count(*) as total'))
                        ->groupBy('id_lokasi')
                        ->with('lokasi')
                        ->get();

        return view('admin.dashboard.index', compact('stats', 'grafikData', 'transaksiTerbaru', 'perLokasi'));
    }
}
