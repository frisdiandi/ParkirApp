@extends('layouts.petugas')

@section('title', 'Profil Saya')

@push('styles')
<style>
.profile-hero {
    background: linear-gradient(135deg, var(--navy) 0%, var(--blue-light) 100%);
    border-radius: 20px; padding: 24px 20px 22px;
    margin-bottom: 16px; text-align: center;
    color: #fff; position: relative; overflow: hidden;
}
.profile-hero::after {
    content: ''; position: absolute; right: -40px; top: -40px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.profile-hero::before {
    content: ''; position: absolute; left: -30px; bottom: -50px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(245,158,11,.12);
}
.profile-hero > * { position: relative; z-index: 1; }

.avatar-ring {
    width: 96px; height: 96px; margin: 0 auto 14px;
    border-radius: 50%; padding: 4px;
    background: linear-gradient(135deg, var(--accent), #fbbf24);
}
.avatar-inner {
    width: 100%; height: 100%; border-radius: 50%;
    overflow: hidden; background: #fff;
    display: flex; align-items: center; justify-content: center;
}
.avatar-inner img { width: 100%; height: 100%; object-fit: cover; }
.avatar-inner .initials {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, var(--navy), var(--blue-light));
    color: #fff; font-size: 2.2rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}

.profile-hero h2 { font-size: 1.15rem; font-weight: 800; margin-bottom: 3px; }
.profile-hero .uname { font-size: .82rem; color: rgba(255,255,255,.7); }

.role-badges { display: flex; justify-content: center; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
.role-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 99px;
    font-size: .72rem; font-weight: 700;
}
.role-badge.petugas { background: rgba(255,255,255,.2); color: #fff; }
.role-badge.aktif   { background: rgba(16,185,129,.25); color: #6ee7b7; }
.role-badge.aktif .dot { width: 7px; height: 7px; background: #10b981; border-radius: 50%; }

.stat-totals {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px;
}
.stat-totals .it {
    background: var(--white); border-radius: 14px; padding: 14px;
    border: 1px solid var(--gray-100);
}
.stat-totals .it .ic {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; margin-bottom: 8px;
}
.stat-totals .it .val { font-size: 1.3rem; font-weight: 800; color: var(--charcoal); line-height: 1; }
.stat-totals .it.wide .val { font-size: 1rem; }
.stat-totals .it .lbl { font-size: .7rem; color: var(--gray-400); margin-top: 3px; font-weight: 600; }
.stat-totals .it.wide { grid-column: 1 / -1; background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border-color: #bbf7d0; }
.stat-totals .it.wide .ic { background: #10b981; color: #fff; }
.stat-totals .it.wide .val { color: #065f46; }
.stat-totals .it.wide .lbl { color: #047857; }

.info-card {
    background: var(--white); border-radius: 16px;
    border: 1px solid var(--gray-100); overflow: hidden;
    margin-bottom: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.info-card-head {
    padding: 12px 16px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-100);
    font-size: .72rem; font-weight: 800; color: var(--gray-600);
    text-transform: uppercase; letter-spacing: .6px;
    display: flex; align-items: center; gap: 8px;
}
.info-card-head i { color: var(--blue-light); }

.info-row {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--gray-100);
}
.info-row:last-child { border-bottom: none; }
.info-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0;
}
.info-content { flex: 1; min-width: 0; }
.info-label { font-size: .68rem; color: var(--gray-400); font-weight: 600; margin-bottom: 2px; text-transform: uppercase; letter-spacing: .3px; }
.info-value { font-size: .88rem; font-weight: 700; color: var(--charcoal); word-break: break-word; }
.info-value.mono { font-family: 'Courier New', monospace; }

.lokasi-list {
    display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px;
}
.lokasi-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--blue-soft); color: var(--blue-light);
    padding: 5px 11px; border-radius: 20px;
    font-size: .74rem; font-weight: 700;
}

.daily-stats {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    padding: 6px 0;
}
.daily-stats .it {
    text-align: center; padding: 12px 8px;
    border-right: 1px solid var(--gray-100);
}
.daily-stats .it:last-child { border-right: none; }
.daily-stats .it .v { font-size: 1.3rem; font-weight: 800; line-height: 1; }
.daily-stats .it .l { font-size: .68rem; color: var(--gray-400); margin-top: 4px; font-weight: 600; }

.logout-btn {
    width: 100%; padding: 14px;
    background: var(--white);
    border: 1.5px solid var(--danger-soft);
    border-radius: 14px;
    color: var(--danger); font-weight: 800; font-size: .92rem;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    cursor: pointer; transition: all .15s; font-family: inherit;
    margin-top: 6px;
}
.logout-btn:hover { background: var(--danger-soft); }
</style>
@endpush

@section('content')

<div class="profile-hero">
    <div class="avatar-ring">
        <div class="avatar-inner">
            @if($petugas && $petugas->foto)
                <img src="{{ asset('storage/' . $petugas->foto) }}" alt="foto"
                     onerror="this.parentElement.innerHTML='<div class=\'initials\'>{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>'">
            @else
                <div class="initials">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>
            @endif
        </div>
    </div>
    <h2>{{ auth()->user()->nama }}</h2>
    <div class="uname">{{ '@' . auth()->user()->username }}</div>
    <div class="role-badges">
        <span class="role-badge petugas"><i class="fa-solid fa-user-shield"></i> Petugas Parkir</span>
        <span class="role-badge aktif"><span class="dot"></span> Aktif</span>
    </div>
</div>

<div class="stat-totals">
    <div class="it">
        <div class="ic" style="background:var(--blue-soft);color:var(--blue-light);"><i class="fa-solid fa-receipt"></i></div>
        <div class="val">{{ $statTotal['total'] }}</div>
        <div class="lbl">Total Transaksi</div>
    </div>
    <div class="it">
        <div class="ic" style="background:var(--warning-soft);color:var(--warning);"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="val">{{ $statHariIni['total'] }}</div>
        <div class="lbl">Transaksi Hari Ini</div>
    </div>
    <div class="it wide">
        <div class="ic"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="val">Rp {{ number_format($statTotal['pendapatan'], 0, ',', '.') }}</div>
        <div class="lbl">Total Pendapatan</div>
    </div>
</div>

<div class="info-card">
    <div class="info-card-head"><i class="fa-solid fa-id-card"></i> Informasi Akun</div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--blue-soft);color:var(--blue-light);"><i class="fa-solid fa-at"></i></div>
        <div class="info-content">
            <div class="info-label">Username</div>
            <div class="info-value mono">{{ auth()->user()->username }}</div>
        </div>
    </div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--success-soft);color:var(--success);"><i class="fa-solid fa-signature"></i></div>
        <div class="info-content">
            <div class="info-label">Nama Lengkap</div>
            <div class="info-value">{{ auth()->user()->nama }}</div>
        </div>
    </div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--warning-soft);color:var(--warning);"><i class="fa-solid fa-phone"></i></div>
        <div class="info-content">
            <div class="info-label">Kontak / No. HP</div>
            <div class="info-value">{{ auth()->user()->contact ?? '-' }}</div>
        </div>
    </div>
</div>

@if($petugas)
<div class="info-card">
    <div class="info-card-head"><i class="fa-solid fa-briefcase"></i> Informasi Petugas</div>
    <div class="info-row">
        <div class="info-icon" style="background:#fdf4ff;color:#a855f7;"><i class="fa-solid fa-building-columns"></i></div>
        <div class="info-content">
            <div class="info-label">Nomor Rekening</div>
            <div class="info-value mono">{{ $petugas->nomor_rekening ?? '-' }}</div>
        </div>
    </div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--success-soft);color:var(--success);"><i class="fa-solid fa-map-location-dot"></i></div>
        <div class="info-content">
            <div class="info-label">Lokasi Bertugas</div>
            <div class="lokasi-list">
                @forelse($lokasiPetugas as $lok)
                    <span class="lokasi-badge"><i class="fa-solid fa-location-dot" style="font-size:.65rem;"></i> {{ $lok->nama }}</span>
                @empty
                    <span style="color:var(--gray-400);font-size:.82rem;">Belum ada lokasi</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>Data petugas tidak ditemukan. Hubungi admin.</span>
</div>
@endif

<div class="info-card">
    <div class="info-card-head"><i class="fa-solid fa-chart-pie"></i> Statistik Hari Ini</div>
    <div class="daily-stats">
        <div class="it">
            <div class="v" style="color:var(--blue-light);">{{ $statHariIni['total'] }}</div>
            <div class="l">TRANSAKSI</div>
        </div>
        <div class="it">
            <div class="v" style="color:var(--success);">{{ $statHariIni['lunas'] }}</div>
            <div class="l">LUNAS</div>
        </div>
        <div class="it">
            <div class="v" style="color:var(--warning);">{{ $statHariIni['parkir'] }}</div>
            <div class="l">PARKIR</div>
        </div>
    </div>
</div>

<div class="info-card">
    <div class="info-card-head"><i class="fa-solid fa-mobile-screen"></i> Tentang Aplikasi</div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--blue-soft);color:var(--blue-light);"><i class="fa-solid fa-square-parking"></i></div>
        <div class="info-content">
            <div class="info-label">Aplikasi</div>
            <div class="info-value">SIRP — Sistem Retribusi Parkir</div>
        </div>
    </div>
    <div class="info-row">
        <div class="info-icon" style="background:var(--success-soft);color:var(--success);"><i class="fa-solid fa-code-branch"></i></div>
        <div class="info-content">
            <div class="info-label">Versi</div>
            <div class="info-value">1.0.0</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar dari akun?');">
    @csrf
    <button type="submit" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar dari Akun
    </button>
</form>

@endsection
