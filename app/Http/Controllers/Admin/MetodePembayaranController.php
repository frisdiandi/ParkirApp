<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metode = MetodePembayaran::latest()->paginate(10);
        return view('admin.metode.index', compact('metode'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:metode_pembayaran,nama']);
        MetodePembayaran::create($request->only('nama'));
        return redirect()->route('admin.metode.index')->with('success', 'Metode pembayaran ditambahkan.');
    }

    public function update(Request $request, MetodePembayaran $metode)
    {
        $request->validate(['nama' => 'required|string|max:100|unique:metode_pembayaran,nama,' . $metode->id]);
        $metode->update($request->only('nama'));
        return redirect()->route('admin.metode.index')->with('success', 'Metode pembayaran diperbarui.');
    }

    public function destroy(MetodePembayaran $metode)
    {
        $metode->delete();
        return redirect()->route('admin.metode.index')->with('success', 'Metode pembayaran dihapus.');
    }
}
