@extends('layouts.petugas')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ── Hero ──────────────────────────────────────────── */
.hero {
    background: linear-gradient(135deg, #0f2a5e 0%, #1e3a8a 55%, #2563eb 100%);
    border-radius: 22px; padding: 20px 18px 22px;
    margin-bottom: 18px; position: relative; overflow: hidden; color: #fff;
}
.hero::before {
    content: ''; position: absolute;
    width: 180px; height: 180px; border-radius: 50%;
    background: rgba(255,255,255,.05);
    right: -50px; top: -60px; pointer-events: none;
}
.hero::after {
    content: ''; position: absolute;
    width: 90px; height: 90px; border-radius: 50%;
    background: rgba(245,158,11,.12);
    right: 40px; bottom: -30px; pointer-events: none;
}
.hero-top { display: flex; justify-content: space-between; align-items: flex-start; position: relative; z-index: 1; }
.hero-greeting { font-size: 1.1rem; font-weight: 800; line-height: 1.2; }
.hero-date { font-size: .73rem; color: rgba(255,255,255,.65); margin-top: 3px; font-weight: 500; }
.hero-avatar {
    width: 42px; height: 42px; border-radius: 12px;
    background: rgba(255,255,255,.18); border: 2px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; font-weight: 800; flex-shrink: 0; color: #fff;
}

/* Lokasi pill */
.lokasi-pill {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25);
    color: #fff; padding: 8px 14px; border-radius: 99px;
    font-size: .78rem; font-weight: 700; margin-top: 14px;
    cursor: pointer; font-family: inherit;
    position: relative; z-index: 1; width: 100%;
    transition: background .18s; max-width: 100%;
    text-align: left;
}
.lokasi-pill:hover { background: rgba(255,255,255,.22); }
.lokasi-pill .pill-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.lokasi-pill .pill-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #34d399; flex-shrink: 0;
}
.lokasi-pill .pill-dot.none { background: rgba(255,255,255,.4); }

/* ── Stats ────────────────────────────────────────── */
.stats-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 10px; margin-bottom: 18px;
}
.stat-box {
    background: #fff; border-radius: 16px; padding: 15px 13px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 2px 8px rgba(15,42,94,.04);
}
.stat-box .s-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; margin-bottom: 10px;
}
.stat-box .s-val { font-size: 1.5rem; font-weight: 800; color: #1e293b; line-height: 1; }
.stat-box .s-lbl { font-size: .69rem; color: #94a3b8; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }

.stat-income {
    grid-column: 1 / -1;
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border-color: #a7f3d0;
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 16px;
}
.stat-income .s-icon { background: #10b981; color: #fff; margin-bottom: 0; }
.stat-income .s-val  { font-size: 1.4rem; color: #065f46; }
.stat-income .s-lbl  { color: #047857; }
.stat-income-right { text-align: right; }
.stat-income-trend { font-size: 1.6rem; color: rgba(16,185,129,.3); }

/* ── Quick actions ────────────────────────────────── */
.actions-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.action-btn {
    padding: 20px 12px; border-radius: 16px; border: none;
    font-family: inherit; cursor: pointer;
    display: flex; flex-direction: column; align-items: center; gap: 9px;
    font-size: .83rem; font-weight: 800; color: #fff;
    text-decoration: none; box-shadow: 0 4px 16px rgba(0,0,0,.1);
    transition: transform .18s, box-shadow .18s;
    line-height: 1.2; text-align: center;
}
.action-btn i { font-size: 1.6rem; }
.action-btn.masuk  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.action-btn.keluar { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
.action-btn:hover  { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.15); }
.action-btn:active { transform: scale(.97); }

/* ── Section header ───────────────────────────────── */
.sec-hd {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px;
}
.sec-hd h3 {
    font-size: .92rem; font-weight: 800; color: #1e293b;
    display: flex; align-items: center; gap: 8px;
}
.sec-hd a { font-size: .78rem; color: var(--blue-light); font-weight: 700; }

.badge-cnt {
    background: #fef3c7; color: #d97706;
    font-size: .66rem; font-weight: 800;
    padding: 2px 7px; border-radius: 99px; line-height: 1.6;
}

/* ── Parkir item ──────────────────────────────────── */
.park-item {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 13px; background: #fff;
    border-radius: 14px; margin-bottom: 8px;
    border: 1px solid #f1f5f9; color: inherit;
    text-decoration: none;
    box-shadow: 0 1px 4px rgba(15,42,94,.03);
    transition: box-shadow .18s, transform .18s;
}
.park-item:last-child { margin-bottom: 0; }
.park-item:hover { box-shadow: 0 6px 18px rgba(15,42,94,.08); transform: translateY(-1px); }
.park-icon {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    background: #fef3c7; color: #d97706;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.park-icon.motor { background: #dbeafe; color: #2563eb; }
.park-icon.truk  { background: #fce7f3; color: #db2777; }
.park-info { flex: 1; min-width: 0; }
.park-plate {
    font-size: .95rem; font-weight: 800;
    font-family: 'Courier New', monospace; letter-spacing: 1px; color: #1e293b;
}
.park-sub { font-size: .7rem; color: #94a3b8; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.park-time { text-align: right; flex-shrink: 0; }
.park-time-val { font-size: .82rem; font-weight: 700; color: #2563eb; }
.park-time-dur { font-size: .65rem; color: #94a3b8; margin-top: 2px; }

/* ── Empty state ──────────────────────────────────── */
.empty-box {
    text-align: center; padding: 36px 20px; color: #94a3b8;
    background: #fff; border-radius: 16px;
    border: 2px dashed #e2e8f0;
}
.empty-box i { font-size: 2.4rem; display: block; margin-bottom: 10px; color: #cbd5e1; }
.empty-box p { font-size: .82rem; }

/* ── Lokasi overlay (ganti <dialog> native) ───────── */
/* Backdrop — tersembunyi by default, TIDAK pakai <dialog> */
#overlayLokasi {
    display: none;          /* HIDDEN sampai dibuka JS */
    position: fixed;
    inset: 0;
    z-index: 999;
    background: rgba(10,20,50,.5);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    align-items: flex-end;
    justify-content: center;
}
/* Saat aktif */
#overlayLokasi.open {
    display: flex;
}

/* Bottom sheet */
.dlg-sheet {
    background: #fff;
    border-radius: 22px 22px 0 0;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 -8px 40px rgba(15,42,94,.18);
    /* Kurangi tinggi bottom nav (~65px) agar sheet tidak tertutup navbar */
    max-height: calc(85vh - 65px);
    display: flex;
    flex-direction: column;
    animation: sheetUp .25s cubic-bezier(.32,.72,0,1);
}
@keyframes sheetUp {
    from { transform: translateY(100%); }
    to   { transform: translateY(0); }
}

.dlg-handle {
    width: 36px; height: 4px; background: #e2e8f0;
    border-radius: 99px; margin: 12px auto 0; flex-shrink: 0;
}
.dlg-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px 10px; flex-shrink: 0;
}
.dlg-head h4 { font-size: .95rem; font-weight: 800; color: #1e293b; }
.dlg-close {
    width: 30px; height: 30px; border-radius: 8px;
    background: #f1f5f9; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; color: #64748b;
    -webkit-tap-highlight-color: transparent;
    transition: background .15s, transform .12s;
}
.dlg-close:hover  { background: #e2e8f0; }
.dlg-close:active { transform: scale(.9); }

/* Scrollable list lokasi */
.dlg-body {
    padding: 4px 14px 24px;
    flex: 1;
    overflow-y: auto;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
}
.dlg-body::-webkit-scrollbar { width: 4px; }
.dlg-body::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }

.lok-item {
    display: flex; align-items: center; gap: 12px;
    padding: 13px 12px; border-radius: 13px;
    border: 1.5px solid #e2e8f0; background: #fff;
    cursor: pointer; width: 100%; font-family: inherit;
    text-align: left; margin-bottom: 8px;
    transition: border-color .15s, background .15s;
}
.lok-item:last-child { margin-bottom: 0; }
.lok-item:hover  { border-color: #93c5fd; background: #eff6ff; }
.lok-item.active { border-color: #2563eb; background: #eff6ff; }
.lok-ic {
    width: 38px; height: 38px; border-radius: 10px;
    background: #f1f5f9; display: flex; align-items: center;
    justify-content: center; color: #64748b; font-size: .85rem; flex-shrink: 0;
}
.lok-item.active .lok-ic { background: #2563eb; color: #fff; }
.lok-item-info { flex: 1; min-width: 0; }
.lok-item-nama  { font-size: .88rem; font-weight: 700; color: #1e293b; }
.lok-item-coord { font-size: .7rem; color: #94a3b8; margin-top: 2px; }
.lok-check { color: #2563eb; font-size: .85rem; display: none; flex-shrink: 0; }
.lok-item.active .lok-check { display: block; }

.lok-all-btn {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 12px; border-radius: 13px;
    border: 1.5px dashed #e2e8f0; background: transparent;
    cursor: pointer; width: 100%; font-family: inherit;
    text-align: left; margin-bottom: 8px; color: #64748b;
    transition: border-color .15s, background .15s;
}
.lok-all-btn:hover { border-color: #94a3b8; background: #f8fafc; }
.lok-all-btn.active { border-color: #64748b; background: #f8fafc; color: #1e293b; }
</style>
@endpush

@section('content')

{{-- ── HERO ────────────────────────────────────────── --}}
<div class="hero">
    <div class="hero-top">
        <div>
            <div class="hero-greeting">Halo, {{ explode(' ', Auth::user()->nama)[0] }} 👋</div>
            <div class="hero-date">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
        </div>
        <div class="hero-avatar">{{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}</div>
    </div>

    <button class="lokasi-pill" onclick="bukaLokasi()">
        <span class="pill-dot {{ $lokasiAktif ? '' : 'none' }}"></span>
        <span class="pill-name">
            {{ $lokasiAktif ? $lokasiAktif->nama : 'Semua Lokasi Tugas' }}
        </span>
        <i class="fa-solid fa-chevron-down" style="font-size:.6rem;flex-shrink:0;"></i>
    </button>
</div>

{{-- ── STATS ───────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-box">
        <div class="s-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-car"></i></div>
        <div class="s-val">{{ $stats['transaksi_hari_ini'] }}</div>
        <div class="s-lbl">Transaksi Hari Ini</div>
    </div>
    <div class="stat-box">
        <div class="s-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-clock"></i></div>
        <div class="s-val">{{ $stats['kendaraan_parkir'] }}</div>
        <div class="s-lbl">Masih Parkir</div>
    </div>
    <div class="stat-box stat-income">
        <div>
            <div class="s-icon"><i class="fa-solid fa-wallet"></i></div>
        </div>
        <div class="stat-income-right">
            <div class="s-val">Rp {{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}</div>
            <div class="s-lbl">Pendapatan Hari Ini</div>
        </div>
        <i class="fa-solid fa-arrow-trend-up stat-income-trend"></i>
    </div>
</div>

{{-- ── QUICK ACTIONS ───────────────────────────────── --}}
<div class="actions-row">
    <a href="{{ route('petugas.tambah') }}" class="action-btn masuk">
        <i class="fa-solid fa-car-on"></i>
        Kendaraan Masuk
    </a>
    <a href="{{ route('petugas.scan-checkout') }}" class="action-btn keluar">
        <i class="fa-solid fa-car-side"></i>
        Kendaraan Keluar
    </a>
</div>

{{-- ── ACTIVE PARKING ──────────────────────────────── --}}
<div class="sec-hd">
    <h3>
        Sedang Parkir
        @if($aktivParkir->count())
        <span class="badge-cnt">{{ $aktivParkir->count() }}</span>
        @endif
    </h3>
    @if($aktivParkir->count())
    <a href="{{ route('petugas.riwayat', ['status' => '0']) }}">Lihat Semua →</a>
    @endif
</div>

@forelse($aktivParkir as $t)
@php
    $n    = strtolower($t->tarif?->nama ?? '');
    $type = (str_contains($n,'2') || str_contains($n,'motor')) ? 'motor'
          : ((str_contains($n,'6') || str_contains($n,'truk') || str_contains($n,'bus')) ? 'truk' : 'mobil');
    $icon = $type === 'motor' ? 'motorcycle' : ($type === 'truk' ? 'truck' : 'car');
@endphp
<a href="{{ route('petugas.scan-checkout', ['plate' => $t->nomor_polisi]) }}" class="park-item">
    <div class="park-icon {{ $type }}">
        <i class="fa-solid fa-{{ $icon }}"></i>
    </div>
    <div class="park-info">
        <div class="park-plate">{{ $t->nomor_polisi }}</div>
        <div class="park-sub">{{ $t->tarif?->nama }} &middot; {{ $t->lokasi?->nama }}</div>
    </div>
    <div class="park-time">
        <div class="park-time-val">{{ \Carbon\Carbon::parse($t->jam_masuk)->format('H:i') }}</div>
        <div class="park-time-dur">
            {{ \Carbon\Carbon::parse($t->jam_masuk)->diffForHumans(null, ['parts'=>1,'short'=>true,'syntax'=>\Carbon\Carbon::DIFF_ABSOLUTE]) }}
        </div>
    </div>
</a>
@empty
<div class="empty-box">
    <i class="fa-solid fa-square-parking"></i>
    <p>Belum ada kendaraan yang parkir<br>
        {{ $lokasiAktif ? 'di ' . $lokasiAktif->nama : 'hari ini' }}</p>
</div>
@endforelse

{{-- ── LOKASI OVERLAY (bukan <dialog> native) ─────── --}}
{{-- display:none by default, hanya muncul saat bukaLokasi() dipanggil --}}
<div id="overlayLokasi" onclick="tutupLokasiBackdrop(event)">
  <div class="dlg-sheet">
    <div class="dlg-handle"></div>
    <div class="dlg-head">
        <h4><i class="fa-solid fa-map-location-dot" style="color:#2563eb;margin-right:6px;"></i>Pilih Lokasi Tugas</h4>
        <button class="dlg-close" type="button" onclick="tutupLokasi()">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="dlg-body">
        <button type="button" onclick="pilihLokasi(0)"
            class="lok-all-btn {{ !$lokasiAktif ? 'active' : '' }}">
            <div class="lok-ic" style="{{ !$lokasiAktif ? 'background:#475569;color:#fff;' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="lok-item-info">
                <div class="lok-item-nama">Semua Lokasi</div>
                <div class="lok-item-coord">Tampilkan data semua lokasi tugas</div>
            </div>
            @if(!$lokasiAktif)<i class="fa-solid fa-check" style="color:#475569;flex-shrink:0;"></i>@endif
        </button>

        @foreach($semuaLokasi as $l)
        <button type="button" onclick="pilihLokasi({{ $l->id }})"
                class="lok-item {{ $lokasiAktif && $lokasiAktif->id == $l->id ? 'active' : '' }}">
            <div class="lok-ic"><i class="fa-solid fa-map-location-dot"></i></div>
            <div class="lok-item-info">
                <div class="lok-item-nama">{{ $l->nama }}</div>
                @if($l->koordinat)
                <div class="lok-item-coord">{{ $l->koordinat }}</div>
                @endif
            </div>
            <i class="fa-solid fa-check lok-check"></i>
        </button>
        @endforeach
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function bukaLokasi() {
    document.getElementById('overlayLokasi').classList.add('open');
    document.body.style.overflow = 'hidden'; // cegah scroll halaman saat overlay aktif
}

function tutupLokasi() {
    document.getElementById('overlayLokasi').classList.remove('open');
    document.body.style.overflow = '';
}

// Tutup saat klik backdrop (area gelap), bukan saat klik sheet
function tutupLokasiBackdrop(e) {
    if (e.target === document.getElementById('overlayLokasi')) {
        tutupLokasi();
    }
}

function pilihLokasi(id) {
    tutupLokasi();
    window.location.href = '{{ route('petugas.dashboard') }}?lokasi_id=' + id;
}
</script>
@endpush