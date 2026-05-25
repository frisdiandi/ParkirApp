@extends('layouts.petugas')

@section('title', 'Pembayaran Berhasil')

@push('styles')
<style>
.sukses-wrap {
    min-height: calc(100vh - 56px - 80px);
    display: flex; flex-direction: column;
    align-items: center; justify-content: flex-start;
    text-align: center;
    padding: 20px 4px;
    position: relative; overflow: hidden;
}

/* Confetti */
.confetti {
    position: absolute; width: 10px; height: 14px;
    top: -20px; opacity: 0; pointer-events: none;
    animation: confettiFall linear forwards;
}
@keyframes confettiFall {
    0%   { transform: translateY(0) rotate(0); opacity: 1; }
    100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}

/* Checkmark animation */
.success-circle {
    width: 130px; height: 130px; border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex; align-items: center; justify-content: center;
    margin: 30px auto 20px;
    box-shadow: 0 12px 40px rgba(16,185,129,0.45);
    position: relative;
    animation: scaleIn .55s cubic-bezier(.2,.9,.3,1.4);
}
.success-circle::before {
    content: ''; position: absolute; inset: -10px;
    border-radius: 50%; border: 4px solid rgba(16,185,129,0.25);
    animation: ringPulse 2s ease-out infinite;
}
.success-circle::after {
    content: ''; position: absolute; inset: -20px;
    border-radius: 50%; border: 2px solid rgba(16,185,129,0.15);
    animation: ringPulse 2s ease-out infinite .4s;
}
@keyframes ringPulse {
    0%   { transform: scale(.9); opacity: 1; }
    100% { transform: scale(1.4); opacity: 0; }
}
@keyframes scaleIn {
    0%   { transform: scale(0) rotate(-90deg); opacity: 0; }
    70%  { transform: scale(1.15) rotate(8deg); opacity: 1; }
    100% { transform: scale(1) rotate(0); opacity: 1; }
}

/* Checkmark drawing */
.check-svg {
    width: 65px; height: 65px;
    stroke: white; stroke-width: 6; fill: none;
    stroke-linecap: round; stroke-linejoin: round;
}
.check-svg path {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: drawCheck .6s ease forwards .35s;
}
@keyframes drawCheck { to { stroke-dashoffset: 0; } }

.sukses-title {
    font-size: 1.6rem; font-weight: 800; color: var(--charcoal);
    margin-bottom: 6px; opacity: 0;
    animation: slideUp .5s ease forwards .55s;
}
.sukses-subtitle {
    font-size: .92rem; color: var(--gray-500);
    margin-bottom: 22px; opacity: 0;
    animation: slideUp .5s ease forwards .7s;
}
@keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

.receipt {
    background: var(--white); border-radius: 18px;
    border: 1px solid var(--gray-100);
    padding: 0; width: 100%; max-width: 380px;
    margin-bottom: 16px;
    box-shadow: 0 6px 24px rgba(15,42,94,0.07);
    overflow: hidden;
    opacity: 0; animation: slideUp .5s ease forwards .85s;
    position: relative;
}
.receipt::after {
    content: ''; position: absolute;
    bottom: -8px; left: 0; right: 0; height: 14px;
    background: radial-gradient(circle, transparent 0, transparent 5px, var(--gray-50) 5px);
    background-size: 14px 14px;
    background-position: 0 0;
}

.receipt-head {
    background: linear-gradient(135deg, var(--navy), var(--blue-light));
    padding: 16px 18px; color: #fff;
}
.receipt-head .lbl { font-size: .68rem; opacity: .8; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; }
.receipt-head .ref { font-size: .95rem; font-weight: 800; margin-top: 3px; font-family: 'Courier New', monospace; }

.receipt-plate {
    text-align: center; padding: 18px 16px;
    background: var(--gray-50); border-bottom: 1px dashed var(--gray-200);
}
.receipt-plate .l { font-size: .65rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase; letter-spacing: .8px; }
.receipt-plate .v {
    font-family: 'Courier New', monospace; font-size: 1.55rem;
    font-weight: 800; color: var(--navy); letter-spacing: 2px;
    margin-top: 6px;
}

.receipt-body { padding: 4px 18px; }
.r-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 0; border-bottom: 1px solid var(--gray-100);
    font-size: .85rem;
}
.r-row:last-child { border-bottom: none; }
.r-row .l { color: var(--gray-500); font-weight: 600; }
.r-row .v { font-weight: 700; color: var(--charcoal); }

.receipt-total {
    margin: 4px 18px 22px;
    padding: 14px 16px;
    background: linear-gradient(135deg, #d1fae5, #ecfdf5);
    border-radius: 12px;
    display: flex; justify-content: space-between; align-items: center;
}
.receipt-total .l { color: #047857; font-weight: 700; font-size: .9rem; }
.receipt-total .v { color: #065f46; font-weight: 800; font-size: 1.3rem; }

.actions {
    display: flex; flex-direction: column; gap: 8px;
    width: 100%; max-width: 380px;
    opacity: 0; animation: slideUp .5s ease forwards 1s;
}

/* Auto-redirect indicator */
.auto-back {
    margin-top: 10px; font-size: .78rem; color: var(--gray-400);
    opacity: 0; animation: slideUp .5s ease forwards 1.2s;
}
</style>
@endpush

@section('content')

<div class="sukses-wrap" id="suksesWrap">

    <div class="success-circle">
        <svg class="check-svg" viewBox="0 0 52 52">
            <path d="M14 27 L22 35 L38 19"/>
        </svg>
    </div>

    <h1 class="sukses-title">Pembayaran Berhasil!</h1>
    <p class="sukses-subtitle">Kendaraan <strong>{{ $transaksi->nomor_polisi }}</strong> telah keluar parkir</p>

    <div class="receipt">
        <div class="receipt-head">
            <div class="lbl">Nomor Referensi</div>
            <div class="ref">{{ $transaksi->reference_number }}</div>
        </div>
        <div class="receipt-plate">
            <div class="l">Plat Nomor</div>
            <div class="v">{{ $transaksi->nomor_polisi }}</div>
        </div>
        <div class="receipt-body">
            <div class="r-row"><span class="l"><i class="fa-solid fa-location-dot text-primary"></i> Lokasi</span><span class="v">{{ $transaksi->lokasi->nama ?? '-' }}</span></div>
            <div class="r-row"><span class="l"><i class="fa-solid fa-tag text-primary"></i> Tarif</span><span class="v">{{ $transaksi->tarif->nama ?? '-' }}</span></div>
            <div class="r-row"><span class="l"><i class="fa-solid fa-clock text-primary"></i> Jam Masuk</span><span class="v">{{ \Carbon\Carbon::parse($transaksi->jam_masuk)->format('H:i') }}</span></div>
            <div class="r-row"><span class="l"><i class="fa-solid fa-clock-rotate-left text-primary"></i> Jam Keluar</span><span class="v">{{ $transaksi->jam_keluar ? \Carbon\Carbon::parse($transaksi->jam_keluar)->format('H:i') : '-' }}</span></div>
            <div class="r-row"><span class="l"><i class="fa-solid fa-hourglass-half text-primary"></i> Durasi</span><span class="v text-warning">{{ $durasi }}</span></div>
            <div class="r-row"><span class="l"><i class="fa-solid fa-wallet text-primary"></i> Metode</span><span class="v">{{ $transaksi->metodePembayaran->nama ?? '-' }}</span></div>
            @if($transaksi->customer_name)
            <div class="r-row"><span class="l"><i class="fa-solid fa-user text-primary"></i> Pembayar</span><span class="v" style="font-size:.78rem;">{{ $transaksi->customer_name }}</span></div>
            @endif
            <div class="r-row"><span class="l"><i class="fa-solid fa-calendar text-primary"></i> Tanggal</span><span class="v">{{ \Carbon\Carbon::parse($transaksi->tgl)->translatedFormat('d F Y') }}</span></div>
        </div>
    </div>

    <div class="receipt-total" style="opacity:0;animation: slideUp .5s ease forwards .9s;">
        <span class="l">Total Dibayar</span>
        <span class="v">Rp {{ number_format($transaksi->tarif->tarif ?? 0, 0, ',', '.') }}</span>
    </div>

    <div class="actions">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-primary" style="padding:14px;">
            <i class="fa-solid fa-house"></i> Kembali ke Dashboard
        </a>
        <a href="{{ route('petugas.scan-checkout') }}" class="btn btn-secondary" style="padding:13px;">
            <i class="fa-solid fa-car-side"></i> Checkout Kendaraan Lain
        </a>
    </div>

    <p class="auto-back">Otomatis kembali ke dashboard dalam <span id="countdownBack">10</span> detik</p>
</div>

@endsection

@push('scripts')
<script>
// Confetti generator
(function() {
    const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
    const wrap = document.getElementById('suksesWrap');
    for (let i = 0; i < 40; i++) {
        const el = document.createElement('div');
        el.className = 'confetti';
        el.style.left = Math.random() * 100 + '%';
        el.style.background = colors[Math.floor(Math.random() * colors.length)];
        el.style.animationDuration = (2.5 + Math.random() * 2) + 's';
        el.style.animationDelay = (Math.random() * 0.8) + 's';
        el.style.transform = `rotate(${Math.random() * 360}deg)`;
        wrap.appendChild(el);
    }
})();

// Auto-redirect ke dashboard setelah 10 detik
let secs = 10;
const cd = document.getElementById('countdownBack');
const tm = setInterval(() => {
    secs--;
    if (cd) cd.textContent = secs;
    if (secs <= 0) {
        clearInterval(tm);
        window.location.href = '{{ route('petugas.dashboard') }}';
    }
}, 1000);
</script>
@endpush
