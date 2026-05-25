@extends('layouts.petugas')
@section('title', 'Dashboard Petugas')

@push('styles')
<style>
.hero-banner {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue-light) 100%);
    border-radius: 20px; padding: 18px 18px 20px;
    margin-bottom: 16px; position: relative; overflow: hidden;
    color: var(--white);
}
.hero-banner::after {
    content: ''; position: absolute; right: -30px; top: -30px;
    width: 140px; height: 140px; border-radius: 50%;
    background: rgba(255,255,255,.07);
}
.hero-banner::before {
    content: ''; position: absolute; right: 30px; bottom: -40px;
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(245,158,11,.15);
}
.hero-banner h2 { font-size: 1.05rem; font-weight: 800; margin-bottom: 3px; position: relative; z-index: 1; }
.hero-banner p  { font-size: .76rem; color: rgba(255,255,255,.7); position: relative; z-index: 1; }
.lokasi-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.18); color: var(--white);
    padding: 7px 13px; border-radius: 99px; font-size: .76rem;
    font-weight: 700; margin-top: 12px; cursor: pointer;
    border: none; font-family: inherit; position: relative; z-index: 1;
    transition: background .2s;
}
.lokasi-chip:hover { background: rgba(255,255,255,.28); }

.stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
.stat-card {
    background: var(--white); border-radius: 14px; padding: 14px 12px;
    border: 1px solid var(--gray-100);
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.stat-card .icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; margin-bottom: 10px;
}
.stat-card .val { font-size: 1.45rem; font-weight: 800; line-height: 1; color: var(--charcoal); }
.stat-card .lbl { font-size: .7rem; color: var(--gray-400); margin-top: 3px; font-weight: 600; }

.stat-wide {
    grid-column: 1 / -1;
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    border-color: #bbf7d0;
}
.stat-wide .head { display:flex; justify-content:space-between; align-items:center; }
.stat-wide .icon { background: #10b981; color: #fff; }
.stat-wide .val  { font-size: 1.35rem; color: #065f46; }
.stat-wide .lbl  { color: #047857; }

.quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.quick-btn {
    padding: 18px 12px; border-radius: 14px; border: none;
    font-family: inherit; cursor: pointer;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    font-size: .82rem; font-weight: 800; transition: all .2s;
    text-decoration: none; color: var(--white);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.quick-btn i { font-size: 1.5rem; }
.quick-btn.check-in  { background: linear-gradient(135deg, #10b981, #059669); }
.quick-btn.check-out { background: linear-gradient(135deg, #2563eb, #1e40af); }
.quick-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.15); }
.quick-btn:active { transform: scale(.98); }

.parkir-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px; background: var(--white);
    border-radius: 14px; margin-bottom: 8px;
    border: 1px solid var(--gray-100); color: inherit;
    transition: all .2s;
}
.parkir-item:hover { box-shadow: 0 4px 14px rgba(0,0,0,.07); transform: translateY(-1px); }
.parkir-item-icon {
    width: 44px; height: 44px; border-radius: 11px; flex-shrink: 0;
    background: var(--warning-soft); color: var(--warning);
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
}
.parkir-item-info { flex: 1; min-width: 0; }
.parkir-item-plate { font-size: .95rem; font-weight: 800; font-family: 'Courier New', monospace; letter-spacing: 1px; color: var(--charcoal); }
.parkir-item-sub   { font-size: .72rem; color: var(--gray-400); margin-top: 3px; }
.parkir-item-time  { font-size: .82rem; font-weight: 700; color: var(--blue-light); text-align: right; }
.parkir-item-dur   { font-size: .65rem; color: var(--gray-400); text-align: right; margin-top: 2px; }

.empty-state {
    text-align: center; padding: 36px 20px; color: var(--gray-400);
    background: var(--white); border-radius: 14px;
    border: 1.5px dashed var(--gray-200);
}
.empty-state i { font-size: 2.5rem; display: block; margin-bottom: 10px; color: var(--gray-300); }
.empty-state p { font-size: .85rem; }

.lokasi-option {
    display:flex; align-items:center; gap:12px; padding:13px;
    border-radius:12px; border:1.5px solid var(--gray-200); background:var(--white);
    cursor:pointer; font-family:inherit; text-align:left; transition:all .15s; width:100%;
}
.lokasi-option:hover { border-color: var(--blue-light); background: var(--blue-soft); }
.lokasi-option.active { border-color: var(--blue-light); background: var(--blue-soft); }
.lokasi-option .ic {
    width:38px; height:38px; background:var(--gray-100); border-radius:10px;
    display:flex; align-items:center; justify-content:center; color:var(--blue-light); flex-shrink:0;
}
.lokasi-option.active .ic { background: var(--blue-light); color: var(--white); }
</style>
@endpush

@section('content')

<div class="hero-banner">
    <h2>Halo, {{ explode(' ', Auth::user()->nama)[0] }} 👋</h2>
    <p>{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    <button class="lokasi-chip" onclick="document.getElementById('pilihLokasi').showModal()">
        <i class="fa-solid fa-location-dot"></i>
        <span id="lokasiAktif">Pilih Lokasi Tugas</span>
        <i class="fa-solid fa-chevron-down" style="font-size:.6rem;"></i>
    </button>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="icon" style="background:var(--blue-soft);color:var(--blue-light);"><i class="fa-solid fa-car"></i></div>
        <div class="val">{{ $stats['transaksi_hari_ini'] }}</div>
        <div class="lbl">Transaksi Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="icon" style="background:var(--warning-soft);color:#d97706;"><i class="fa-solid fa-clock"></i></div>
        <div class="val">{{ $stats['kendaraan_parkir'] }}</div>
        <div class="lbl">Masih Parkir</div>
    </div>
    <div class="stat-card stat-wide">
        <div class="head">
            <div>
                <div class="icon"><i class="fa-solid fa-wallet"></i></div>
                <div class="val">Rp {{ number_format($stats['pendapatan_hari_ini'],0,',','.') }}</div>
                <div class="lbl">Pendapatan Hari Ini</div>
            </div>
            <i class="fa-solid fa-arrow-trend-up" style="font-size:2rem;color:rgba(16,185,129,.25);"></i>
        </div>
    </div>
</div>

<div class="quick-actions">
    <a href="{{ route('petugas.tambah') }}" class="quick-btn check-in">
        <i class="fa-solid fa-car-on"></i>
        Kendaraan Masuk
    </a>
    <a href="{{ route('petugas.scan-checkout') }}" class="quick-btn check-out">
        <i class="fa-solid fa-car-side"></i>
        Kendaraan Keluar
    </a>
</div>

<div class="section-hd">
    <h3>Sedang Parkir
        @if($aktivParkir->count())<span class="badge badge-warning">{{ $aktivParkir->count() }}</span>@endif
    </h3>
    @if($aktivParkir->count())
    <a href="{{ route('petugas.riwayat', ['status' => '0']) }}">Lihat Semua →</a>
    @endif
</div>

@forelse($aktivParkir as $t)
@php
    $n = strtolower($t->tarif?->nama ?? '');
    $icon = (str_contains($n,'2') || str_contains($n,'motor')) ? 'motorcycle'
          : ((str_contains($n,'6') || str_contains($n,'truk') || str_contains($n,'bus')) ? 'truck' : 'car');
@endphp
<a href="{{ route('petugas.scan-checkout', ['plate' => $t->nomor_polisi]) }}" class="parkir-item">
    <div class="parkir-item-icon"><i class="fa-solid fa-{{ $icon }}"></i></div>
    <div class="parkir-item-info">
        <div class="parkir-item-plate">{{ $t->nomor_polisi }}</div>
        <div class="parkir-item-sub">{{ $t->tarif?->nama }} · {{ $t->lokasi?->nama }}</div>
    </div>
    <div>
        <div class="parkir-item-time">{{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }}</div>
        <div class="parkir-item-dur">
            {{ \Carbon\Carbon::parse($t->jam_masuk)->diffForHumans(null, ['parts'=>1,'short'=>true,'syntax'=>\Carbon\Carbon::DIFF_ABSOLUTE]) }}
        </div>
    </div>
</a>
@empty
<div class="empty-state">
    <i class="fa-solid fa-square-parking"></i>
    <p>Belum ada kendaraan yang parkir hari ini</p>
</div>
@endforelse

<dialog id="pilihLokasi">
    <div style="padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h4 style="font-size:1rem;font-weight:800;color:var(--charcoal);">Pilih Lokasi Tugas</h4>
            <button onclick="document.getElementById('pilihLokasi').close()" style="border:none;background:var(--gray-100);border-radius:10px;width:32px;height:32px;cursor:pointer;font-size:.85rem;color:var(--gray-500);">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;max-height:60vh;overflow-y:auto;">
            @forelse($lokasi as $l)
            <button onclick="setLokasi({{ $l->id }}, '{{ addslashes($l->nama) }}')" data-id="{{ $l->id }}" class="lokasi-option">
                <div class="ic"><i class="fa-solid fa-map-location-dot"></i></div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.88rem;font-weight:700;color:var(--charcoal);">{{ $l->nama }}</div>
                    @if($l->koordinat)
                    <div style="font-size:.7rem;color:var(--gray-400);margin-top:2px;">📍 {{ $l->koordinat }}</div>
                    @endif
                </div>
                <i class="fa-solid fa-check check-ic" style="color:var(--blue-light);display:none;"></i>
            </button>
            @empty
            <div class="empty-state"><i class="fa-solid fa-map"></i><p>Tidak ada lokasi tugas</p></div>
            @endforelse
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
let lokasiAktifId = localStorage.getItem('lokasiAktif') || '';
let lokasiAktifNama = localStorage.getItem('lokasiAktifNama') || 'Pilih Lokasi Tugas';

function setLokasi(id, nama) {
    localStorage.setItem('lokasiAktif', id);
    localStorage.setItem('lokasiAktifNama', nama);
    document.getElementById('lokasiAktif').textContent = nama;
    document.getElementById('pilihLokasi').close();
    paintLokasi(id);
}

function paintLokasi(id) {
    document.querySelectorAll('.lokasi-option').forEach(el => {
        const active = el.dataset.id == id;
        el.classList.toggle('active', active);
        const ic = el.querySelector('.check-ic');
        if (ic) ic.style.display = active ? '' : 'none';
    });
}

if (lokasiAktifId) {
    document.getElementById('lokasiAktif').textContent = lokasiAktifNama;
    setTimeout(() => paintLokasi(lokasiAktifId), 50);
}
</script>
@endpush
