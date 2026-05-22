@extends('layouts.petugas')

@section('title', 'Profil Saya')

@push('styles')
<style>
.avatar-ring {
    width: 90px; height: 90px;
    border-radius: 50%;
    border: 3px solid #2563eb;
    padding: 3px;
    background: #fff;
}
.avatar-ring img {
    width: 100%; height: 100%;
    border-radius: 50%; object-fit: cover;
}
.avatar-ring .initials {
    width: 100%; height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff;
}

.info-card {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 12px;
}
.info-card-header {
    padding: 12px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child { border-bottom: none; }
.info-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.info-label { font-size: 0.72rem; color: #94a3b8; margin-bottom: 2px; }
.info-value { font-size: 0.88rem; font-weight: 600; color: #1e293b; }

.lokasi-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: #eff6ff; color: #2563eb;
    padding: 4px 10px; border-radius: 20px;
    font-size: 0.75rem; font-weight: 600;
    margin: 2px;
}

.logout-btn {
    width: 100%;
    padding: 14px;
    background: #fff;
    border: 1.5px solid #fecaca;
    border-radius: 14px;
    color: #dc2626;
    font-weight: 700;
    font-size: 0.9rem;
    display: flex; align-items: center; justify-content: center; gap-8px;
    cursor: pointer;
    transition: background 0.15s;
}
.logout-btn:active { background: #fff5f5; }
</style>
@endpush

@section('content')
<div class="px-4 pt-2 pb-28">

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-[#1e3a5f] to-[#2563eb] rounded-2xl p-6 mb-5 flex flex-col items-center text-center text-white">
        <div class="avatar-ring mb-3">
            @if($petugas && $petugas->foto)
                <img src="{{ asset('storage/' . $petugas->foto) }}" alt="foto">
            @else
                <div class="initials">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>
            @endif
        </div>
        <h2 class="text-lg font-bold mb-1">{{ auth()->user()->nama }}</h2>
        <p class="text-blue-200 text-sm">@{{ auth()->user()->username }}</p>
        <div class="mt-3 flex items-center gap-2">
            <span class="bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full">
                👷 Petugas Parkir
            </span>
            <span class="bg-green-400/20 text-green-300 text-xs font-bold px-3 py-1 rounded-full">
                ● Aktif
            </span>
        </div>
    </div>

    {{-- Info Akun --}}
    <div class="info-card">
        <div class="info-card-header">Informasi Akun</div>
        <div class="info-row">
            <div class="info-icon" style="background:#eff6ff">👤</div>
            <div>
                <div class="info-label">Username</div>
                <div class="info-value">{{ auth()->user()->username }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#f0fdf4">📋</div>
            <div>
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">{{ auth()->user()->nama }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#fef3c7">📞</div>
            <div>
                <div class="info-label">Kontak / No. HP</div>
                <div class="info-value">{{ auth()->user()->contact ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Info Petugas --}}
    @if($petugas)
    <div class="info-card">
        <div class="info-card-header">Informasi Petugas</div>
        <div class="info-row">
            <div class="info-icon" style="background:#fdf4ff">🏦</div>
            <div>
                <div class="info-label">Nomor Rekening</div>
                <div class="info-value font-mono">{{ $petugas->nomor_rekening ?? '-' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#f0fdf4">📍</div>
            <div>
                <div class="info-label">Lokasi Bertugas</div>
                <div class="mt-1">
                    @forelse($lokasiPetugas as $lok)
                        <span class="lokasi-badge">📍 {{ $lok->nama }}</span>
                    @empty
                        <span class="text-slate-400 text-sm">Belum ada lokasi</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Statistik Hari Ini --}}
    <div class="info-card">
        <div class="info-card-header">Statistik Hari Ini</div>
        <div class="grid grid-cols-3 divide-x divide-slate-100">
            <div class="text-center py-4">
                <div class="text-xl font-black text-blue-700">{{ $statHariIni['total'] }}</div>
                <div class="text-xs text-slate-500 mt-1">Transaksi</div>
            </div>
            <div class="text-center py-4">
                <div class="text-xl font-black text-green-700">{{ $statHariIni['lunas'] }}</div>
                <div class="text-xs text-slate-500 mt-1">Lunas</div>
            </div>
            <div class="text-center py-4">
                <div class="text-xl font-black text-orange-600">{{ $statHariIni['parkir'] }}</div>
                <div class="text-xs text-slate-500 mt-1">Parkir</div>
            </div>
        </div>
    </div>

    {{-- App Info --}}
    <div class="info-card mb-5">
        <div class="info-card-header">Tentang Aplikasi</div>
        <div class="info-row">
            <div class="info-icon" style="background:#eff6ff">🅿️</div>
            <div>
                <div class="info-label">Aplikasi</div>
                <div class="info-value">SIRP — Sistem Retribusi Parkir</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:#f0fdf4">🔖</div>
            <div>
                <div class="info-label">Versi</div>
                <div class="info-value">1.0.0</div>
            </div>
        </div>
    </div>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar?')">
        @csrf
        <button type="submit" class="logout-btn">
            <span style="margin-right:8px">🚪</span> Keluar dari Akun
        </button>
    </form>

</div>
@endsection
