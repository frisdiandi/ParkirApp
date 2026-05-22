<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Transaksi, Lokasi, Petugas};
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['petugas.user', 'lokasi', 'tarif', 'metode'])->latest();

        if ($request->filled('tgl_dari')) {
            $query->whereDate('tgl', '>=', $request->tgl_dari);
        }
        if ($request->filled('tgl_sampai')) {
            $query->whereDate('tgl', '<=', $request->tgl_sampai);
        }
        if ($request->filled('id_lokasi')) {
            $query->where('id_lokasi', $request->id_lokasi);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('nomor_polisi')) {
            $query->where('nomor_polisi', 'like', '%' . $request->nomor_polisi . '%');
        }

        $transaksi = $query->paginate(15)->withQueryString();
        $lokasi    = Lokasi::all();

        $totalPendapatan = $query->clone()->where('status', 1)
                            ->join('tarif', 'transaksi.id_tarif', '=', 'tarif.id')
                            ->sum('tarif.tarif');

        return view('admin.transaksi.index', compact('transaksi', 'lokasi', 'totalPendapatan'));
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['petugas.user', 'lokasi', 'tarif', 'metode']);
        return view('admin.transaksi.show', compact('transaksi'));
    }
}
