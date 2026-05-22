@extends('layouts.petugas')
@section('title', 'Dashboard Petugas')

@push('styles')
<style>
.hero-banner {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue-light) 100%);
    border-radius: 20px; padding: 20px;
    margin-bottom: 16px; position: relative; overflow: hidden;
}
.hero-banner::after {
    content: '';
    position: absolute; right: -20px; top: -20px;
    width: 120px; height: 120px; border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.hero-banner h2 { font-size: 1rem; font-weight: 700; color: var(--white); margin-bottom: 4px; }
.hero-banner p  { font-size: .78rem; color: rgba(255,255,255,.65); }
.lokasi-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.15); color: var(--white);
    padding: 5px 12px; border-radius: 99px; font-size: .75rem;
    font-weight: 600; margin-top: 10px; cursor: pointer;
    border: none; font-family: inherit;
    transition: background .2s;
}
.lokasi-chip:hover { background: rgba(255,255,255,.22); }

.parkir-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px; background: var(--white);
    border-radius: 12px; margin-bottom: 8px;
    border: 1px solid var(--gray-100); text-decoration: none; color: inherit;
    transition: box-shadow .2s;
}
.parkir-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.parkir-item-icon {
    width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
    background: #fef3c7; color: var(--warning);
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.parkir-item-info { flex: 1; min-width: 0; }
.parkir-item-plate { font-size: .92rem; font-weight: 800; font-family: monospace; letter-spacing: .5px; }
.parkir-item-sub   { font-size: .72rem; color: var(--gray-400); margin-top: 2px; }
.parkir-item-time  { font-size: .8rem; font-weight: 600; color: var(--blue-light); flex-shrink: 0; }

.quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.quick-btn {
    padding: 14px 10px; border-radius: 14px; border: none;
    font-family: inherit; cursor: pointer;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    font-size: .78rem; font-weight: 700; transition: all .2s;
    text-decoration: none;
}
.quick-btn i { font-size: 1.3rem; }
.quick-btn.check-in { background: linear-gradient(135deg,#dcfce7,#bbf7d0); color: #15803d; }
.quick-btn.check-out { background: linear-gradient(135deg,#dbeafe,#bfdbfe); color: #1e40af; }
.quick-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.12); }
</style>
@endpush

@section('content')

<!-- Hero -->
<div class="hero-banner">
    <h2>Selamat bertugas, {{ explode(' ', Auth::user()->nama)[0] }}! 👋</h2>
    <p>{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    <button class="lokasi-chip" onclick="document.getElementById('pilihLokasi').showModal()">
        <i class="fa-solid fa-map-location-dot"></i>
        <span id="lokasiAktif">Pilih Lokasi Tugas</span>
        <i class="fa-solid fa-chevron-down" style="font-size:.65rem;"></i>
    </button>
</div>

<!-- Stats -->
<div class="stat-row">
    <div class="stat-card-mobile">
        <div class="s-icon" style="background:#dbeafe;color:var(--blue-light);"><i class="fa-solid fa-car"></i></div>
        <div class="s-val">{{ $stats['transaksi_hari_ini'] }}</div>
        <div class="s-lbl">Transaksi Hari Ini</div>
    </div>
    <div class="stat-card-mobile">
        <div class="s-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-clock"></i></div>
        <div class="s-val">{{ $stats['kendaraan_parkir'] }}</div>
        <div class="s-lbl">Masih Parkir</div>
    </div>
    <div class="stat-card-mobile" style="grid-column:1/-1;">
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div class="s-icon" style="background:#dcfce7;color:#15803d;display:inline-flex;margin-bottom:8px;"><i class="fa-solid fa-wallet"></i></div>
                <div class="s-val" style="font-size:1.2rem;">Rp {{ number_format($stats['pendapatan_hari_ini'],0,',','.') }}</div>
                <div class="s-lbl">Pendapatan Hari Ini</div>
            </div>
            <i class="fa-solid fa-arrow-trend-up" style="font-size:2rem;color:var(--gray-100);"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
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

<!-- Active parkir -->
@if($aktivParkir->count())
<div class="section-hd">
    <h3>Sedang Parkir <span class="badge badge-warning">{{ $aktivParkir->count() }}</span></h3>
    <a href="{{ route('petugas.riwayat') }}">Lihat Semua</a>
</div>

@foreach($aktivParkir as $t)
<a href="{{ route('petugas.checkout', $t) }}" class="parkir-item">
    <div class="parkir-item-icon">
        <i class="fa-solid fa-{{ str_contains(strtolower($t->tarif?->nama??''), '2') || str_contains(strtolower($t->tarif?->nama??''), 'motor') ? 'motorcycle' : 'car' }}"></i>
    </div>
    <div class="parkir-item-info">
        <div class="parkir-item-plate">{{ $t->nomor_polisi }}</div>
        <div class="parkir-item-sub">{{ $t->tarif?->nama }} · {{ $t->lokasi?->nama }}</div>
    </div>
    <div>
        <div class="parkir-item-time">{{ $t->jam_masuk }}</div>
        <div style="font-size:.68rem;color:var(--gray-400);text-align:right;margin-top:2px;">
            {{ \Carbon\Carbon::parse($t->jam_masuk)->diffForHumans(null, true) }}
        </div>
    </div>
</a>
@endforeach
@else
<div style="text-align:center;padding:32px 16px;color:var(--gray-400);">
    <i class="fa-solid fa-parking" style="font-size:2.5rem;display:block;margin-bottom:10px;color:var(--gray-200);"></i>
    <p style="font-size:.85rem;">Tidak ada kendaraan yang sedang parkir</p>
</div>
@endif

<!-- Dialog Pilih Lokasi -->
<dialog id="pilihLokasi" style="border:none;border-radius:20px;padding:0;max-width:440px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h4 style="font-size:.95rem;font-weight:700;">Pilih Lokasi Tugas</h4>
            <button onclick="document.getElementById('pilihLokasi').close()" style="border:none;background:var(--gray-100);border-radius:8px;padding:6px 10px;cursor:pointer;font-size:.8rem;">✕</button>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($lokasi as $l)
            <button onclick="setLokasi({{ $l->id }}, '{{ $l->nama }}')"
                    data-id="{{ $l->id }}"
                    style="display:flex;align-items:center;gap:10px;padding:12px;border-radius:12px;border:1.5px solid var(--gray-200);background:var(--white);cursor:pointer;font-family:inherit;text-align:left;transition:all .15s;"
                    class="lokasi-option">
                <div style="width:36px;height:36px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--blue-light);flex-shrink:0;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <div style="font-size:.87rem;font-weight:600;">{{ $l->nama }}</div>
                    @if($l->koordinat)
                    <div style="font-size:.72rem;color:var(--gray-400);">📍 {{ $l->koordinat }}</div>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
let lokasiAktifId = localStorage.getItem('lokasiAktif') || '';
let lokasiAktifNama = localStorage.getItem('lokasiAktifNama') || 'Pilih Lokasi';

function setLokasi(id, nama) {
    localStorage.setItem('lokasiAktif', id);
    localStorage.setItem('lokasiAktifNama', nama);
    lokasiAktifId = id;
    lokasiAktifNama = nama;
    document.getElementById('lokasiAktif').textContent = nama;
    document.getElementById('pilihLokasi').close();

    document.querySelectorAll('.lokasi-option').forEach(el => {
        el.style.borderColor = el.dataset.id == id ? 'var(--blue-light)' : 'var(--gray-200)';
        el.style.background  = el.dataset.id == id ? '#eff6ff' : 'var(--white)';
    });
}

// Init
if (lokasiAktifId) {
    document.getElementById('lokasiAktif').textContent = lokasiAktifNama;
    setTimeout(() => {
        document.querySelectorAll('.lokasi-option').forEach(el => {
            if (el.dataset.id == lokasiAktifId) {
                el.style.borderColor = 'var(--blue-light)';
                el.style.background  = '#eff6ff';
            }
        });
    }, 100);
}
</script>
@endpush
