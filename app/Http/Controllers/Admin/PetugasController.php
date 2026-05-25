<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Petugas, User, Lokasi};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PetugasController extends Controller
{
    public function index()
    {
        $petugas = Petugas::with(['user', 'lokasi'])->latest()->paginate(10);
        return view('admin.petugas.index', compact('petugas'));
    }

    public function create()
    {
        $lokasi = Lokasi::all();
        return view('admin.petugas.create', compact('lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'        => 'required|unique:users,username|max:50',
            'nama'            => 'required|string|max:255',
            'password'        => 'required|min:6|confirmed',
            'contact'         => 'nullable|string|max:20',
            'status'          => 'required|in:0,1',
            'id_lokasi'       => 'nullable|array',
            'id_lokasi.*'     => 'exists:lokasi,id',
            'nomor_rekening'  => 'nullable|string|max:50',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::create([
            'username' => $request->username,
            'nama'     => $request->nama,
            'password' => Hash::make($request->password),
            'contact'  => $request->contact,
            'level'    => 2,
            'status'   => $request->status,
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('petugas', 'public');
        }

        $petugas = Petugas::create([
            'id_user'        => $user->id,
            'nomor_rekening' => $request->nomor_rekening,
            'foto'           => $foto,
        ]);

        $petugas->lokasi()->sync($request->id_lokasi ?? []);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(Petugas $petugas)
    {
        $lokasi = Lokasi::all();
        return view('admin.petugas.edit', compact('petugas', 'lokasi'));
    }

    public function update(Request $request, Petugas $petugas)
    {
        $request->validate([
            'username'        => 'required|unique:users,username,' . $petugas->id_user . '|max:50',
            'nama'            => 'required|string|max:255',
            'password'        => 'nullable|min:6|confirmed',
            'contact'         => 'nullable|string|max:20',
            'status'          => 'required|in:0,1',
            'id_lokasi'       => 'nullable|array',
            'id_lokasi.*'     => 'exists:lokasi,id',
            'nomor_rekening'  => 'nullable|string|max:50',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $userData = [
            'username' => $request->username,
            'nama'     => $request->nama,
            'contact'  => $request->contact,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $petugas->user->update($userData);

        $petugasData = ['nomor_rekening' => $request->nomor_rekening];

        if ($request->hasFile('foto')) {
            if ($petugas->foto) Storage::disk('public')->delete($petugas->foto);
            $petugasData['foto'] = $request->file('foto')->store('petugas', 'public');
        }

        $petugas->update($petugasData);
        $petugas->lokasi()->sync($request->id_lokasi ?? []);

        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil diperbarui.');
    }

    public function destroy(Petugas $petugas)
    {
        if ($petugas->foto) Storage::disk('public')->delete($petugas->foto);
        $petugas->user->delete();
        return redirect()->route('admin.petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}
