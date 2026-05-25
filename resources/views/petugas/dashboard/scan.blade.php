@extends('layouts.petugas')

@section('title', 'Kendaraan Keluar')

@push('styles')
<style>
.page-header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 14px;
}
.page-header .back {
    width: 38px; height: 38px; border-radius: 12px;
    background: var(--white); border: 1px solid var(--gray-200);
    display: flex; align-items: center; justify-content: center;
    color: var(--gray-600);
}
.page-header h2 { font-size: 1.05rem; font-weight: 800; color: var(--charcoal); }
.page-header p  { font-size: .76rem; color: var(--gray-400); margin-top: 1px; }

.tab-switcher {
    display: flex; background: var(--gray-100);
    border-radius: 12px; padding: 4px; margin-bottom: 14px;
}
.tab-btn {
    flex: 1; padding: 9px 8px; border: none; background: transparent;
    cursor: pointer; font-family: inherit; font-size: .82rem;
    font-weight: 700; color: var(--gray-500); border-radius: 9px;
    transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 6px;
}
.tab-btn.active { background: var(--white); color: var(--blue-light); box-shadow: 0 2px 6px rgba(0,0,0,.08); }

/* Scan area */
.scan-wrapper {
    position: relative; background: #000;
    border-radius: 16px;
    overflow: hidden; aspect-ratio: 4/3; max-height: 340px;
    width: 100%; margin-bottom: 14px;
}
#videoScan { width: 100%; height: 100%; object-fit: cover; display: block; }
.scan-overlay {
    position: absolute; inset: 0; display: flex;
    align-items: center; justify-content: center; pointer-events: none;
}
.scan-frame {
    width: 78%; max-width: 280px; height: 38%; max-height: 110px;
    border: 2px solid rgba(255,255,255,.4); border-radius: 10px;
    position: relative; box-shadow: 0 0 0 9999px rgba(0,0,0,.4);
}
.scan-frame::before { content:''; position:absolute; width:22px; height:22px; top:-3px; left:-3px; border-top:3px solid var(--accent); border-left:3px solid var(--accent); border-radius:6px 0 0 0; }
.scan-frame::after  { content:''; position:absolute; width:22px; height:22px; bottom:-3px; right:-3px; border-bottom:3px solid var(--accent); border-right:3px solid var(--accent); border-radius:0 0 6px 0; }
.scan-frame .c-tr   { position:absolute; width:22px; height:22px; top:-3px; right:-3px; border-top:3px solid var(--accent); border-right:3px solid var(--accent); border-radius:0 6px 0 0; }
.scan-frame .c-bl   { position:absolute; width:22px; height:22px; bottom:-3px; left:-3px; border-bottom:3px solid var(--accent); border-left:3px solid var(--accent); border-radius:0 0 6px 0; }
.scan-line {
    position: absolute; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    animation: scanAnim 1.8s ease-in-out infinite;
}
@keyframes scanAnim { 0% { top: 0; } 50% { top: calc(100% - 2px); } 100% { top: 0; } }
.scan-hint { position: absolute; bottom: 10px; left: 0; right: 0; text-align: center; color: rgba(255,255,255,.85); font-size: .75rem; font-weight: 600; }

.cam-actions { display: grid; grid-template-columns: 1fr auto; gap: 8px; margin-bottom: 12px; }
.cam-btn {
    height: 46px; border-radius: 12px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-family: inherit; font-size: .85rem; font-weight: 700;
}
.cam-btn.primary { background: linear-gradient(135deg, var(--blue-light), var(--navy-mid)); color: #fff; }
.cam-btn.ghost { width: 46px; background: var(--gray-100); color: var(--gray-700); }

.processing-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.7);
    display: none; align-items: center; justify-content: center; flex-direction: column;
    color: #fff; gap: 12px; z-index: 5;
}
.processing-overlay.show { display: flex; }
.processing-overlay .spinner { border-color: rgba(255,255,255,.3); border-top-color: #fff; }

/* Manual search */
.manual-search { display: flex; gap: 8px; margin-bottom: 12px; }
.manual-search input {
    flex: 1; padding: 12px 14px; border: 1.5px solid var(--gray-200);
    border-radius: 12px; font-family: 'Courier New', monospace;
    font-size: 1rem; outline: none; text-transform: uppercase;
    font-weight: 700; letter-spacing: 1.5px;
}
.manual-search input:focus { border-color: var(--blue-light); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
.manual-search button {
    padding: 12px 18px; background: linear-gradient(135deg, var(--blue-light), var(--navy-mid));
    color: #fff; border: none; border-radius: 12px; font-weight: 700; font-family: inherit;
    cursor: pointer;
}

.manual-suggest {
    background: var(--white); border: 1px solid var(--gray-100);
    border-radius: 12px; padding: 8px; margin-bottom: 12px;
    max-height: 240px; overflow-y: auto;
}
.suggest-item {
    display: flex; align-items: center; gap: 10px; padding: 10px;
    border-radius: 8px; cursor: pointer; transition: background .15s;
}
.suggest-item:hover { background: var(--gray-50); }
.suggest-item .ic { width: 32px; height: 32px; border-radius: 8px; background: var(--warning-soft); color: var(--warning); display: flex; align-items: center; justify-content: center; }
.suggest-item .info { flex: 1; min-width: 0; }
.suggest-item .pl { font-family: 'Courier New', monospace; font-weight: 800; font-size: .92rem; color: var(--charcoal); letter-spacing: 1px; }
.suggest-item .sb { font-size: .7rem; color: var(--gray-400); }
.suggest-item .ar { color: var(--blue-light); }

/* Trx card */
.trx-card {
    background: var(--white); border-radius: 16px;
    border: 1px solid var(--gray-100); overflow: hidden;
    margin-bottom: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.trx-card-header {
    background: linear-gradient(135deg, var(--navy), var(--blue-light));
    padding: 14px 16px; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
}
.trx-card-header .ref { font-size: .65rem; opacity: .8; font-weight: 600; }
.trx-card-header .ref-val { font-size: .82rem; font-weight: 700; margin-top: 2px; }
.trx-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 11px 16px; border-bottom: 1px solid var(--gray-100); font-size: .85rem;
}
.trx-row:last-child { border-bottom: none; }
.trx-label { color: var(--gray-500); font-weight: 600; }
.trx-value { font-weight: 700; color: var(--charcoal); }
.trx-row.total { background: var(--success-soft); }
.trx-row.total .trx-value { color: #047857; font-size: 1.05rem; font-weight: 800; }

/* Plate big */
.trx-plate {
    text-align: center; padding: 16px;
    background: var(--gray-50); border-bottom: 1px solid var(--gray-100);
}
.trx-plate .lbl { font-size: .65rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 4px; }
.trx-plate .val { font-family: 'Courier New', monospace; font-size: 1.5rem; font-weight: 800; color: var(--navy); letter-spacing: 2px; }

/* Metode pembayaran */
.section-card {
    background: var(--white); border-radius: 16px;
    border: 1px solid var(--gray-100); padding: 14px;
    margin-bottom: 14px;
}
.section-title {
    font-size: .75rem; font-weight: 700; color: var(--gray-500);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px;
}
.metode-btn {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 14px; border: 2px solid var(--gray-200);
    border-radius: 12px; background: var(--white); cursor: pointer;
    width: 100%; margin-bottom: 8px; font-family: inherit; transition: all .2s;
}
.metode-btn:last-child { margin-bottom: 0; }
.metode-btn.selected { border-color: var(--blue-light); background: var(--blue-soft); }
.metode-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
.metode-info { flex: 1; min-width: 0; text-align: left; }
.metode-info .nm { font-weight: 700; font-size: .92rem; color: var(--charcoal); }
.metode-info .ds { font-size: .7rem; color: var(--gray-400); margin-top: 1px; }
.metode-check { width: 22px; height: 22px; border-radius: 50%; border: 2px solid var(--gray-300); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.metode-btn.selected .metode-check { border-color: var(--blue-light); background: var(--blue-light); color: #fff; }

/* ── Confirm dialog ──────────────────────── */
#confirmDialog {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    margin: 0; border: none; padding: 0;
    border-radius: 22px; width: calc(100% - 32px); max-width: 360px;
    box-shadow: 0 20px 60px rgba(15,42,94,.22);
    overflow: hidden;
}
#confirmDialog::backdrop {
    background: rgba(10,20,50,.55);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}
.cdlg-icon {
    width: 64px; height: 64px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.7rem; margin: 0 auto 14px;
}
.cdlg-plate {
    background: #0f2a5e; border-radius: 12px;
    padding: 10px 16px; text-align: center;
    margin-bottom: 14px;
}
.cdlg-plate .lbl { font-size: .62rem; color: rgba(255,255,255,.6); font-weight: 700; text-transform: uppercase; letter-spacing: .8px; }
.cdlg-plate .val { font-family: 'Courier New', monospace; font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: 2px; margin-top: 4px; }
.cdlg-rows { background: #f8fafc; border-radius: 12px; overflow: hidden; margin-bottom: 14px; }
.cdlg-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 9px 14px; font-size: .82rem;
    border-bottom: 1px solid #e2e8f0;
}
.cdlg-row:last-child { border-bottom: none; }
.cdlg-row .rl { color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 7px; }
.cdlg-row .rv { font-weight: 700; color: #1e293b; text-align: right; max-width: 55%; }
.cdlg-total {
    background: linear-gradient(135deg, #ecfdf5, #d1fae5);
    border: 1.5px solid #a7f3d0;
    border-radius: 12px; padding: 12px 16px;
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 16px;
}
.cdlg-total .tl { font-size: .78rem; font-weight: 700; color: #047857; }
.cdlg-total .tv { font-size: 1.25rem; font-weight: 800; color: #065f46; }
.cdlg-actions { display: grid; grid-template-columns: 1fr 1.6fr; gap: 8px; }
.cdlg-actions .btn { padding: 13px 8px; font-size: .88rem; border-radius: 13px; }

/* ── QRIS Dialog ─────────────────────────── */
#qrisDialog {
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    margin: 0; border: none; padding: 0;
    border-radius: 22px; width: calc(100% - 32px); max-width: 360px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(15,42,94,.22);
}
#qrisDialog::backdrop {
    background: rgba(10,20,50,.55);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}
#qrisDialog::-webkit-scrollbar { width: 0; }

.qdlg-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 16px 10px;
}
.qdlg-brand {
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #cd1f2c, #8b0000);
    color: #fff; padding: 6px 13px; border-radius: 8px;
    font-weight: 800; font-size: .82rem; letter-spacing: .5px;
}
.qdlg-close {
    width: 30px; height: 30px; border-radius: 8px;
    background: #f1f5f9; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; color: #64748b;
}

.qris-amount {
    background: #f8fafc; border-radius: 12px;
    padding: 10px 16px; margin: 0 16px 12px;
    display: flex; justify-content: space-between; align-items: center;
}
.qris-amount .l { font-size: .7rem; color: #64748b; font-weight: 600; }
.qris-amount .v { font-size: 1.3rem; font-weight: 800; color: #0f2a5e; }

.qris-img {
    width: 200px; height: 200px; margin: 0 auto 12px;
    background: #fff; border: 3px solid #0f2a5e; border-radius: 14px;
    padding: 6px; display: flex; align-items: center; justify-content: center;
    position: relative;
}
.qris-img canvas { width: 100% !important; height: 100% !important; display: block; }
.qris-img.expired::after {
    content: 'KEDALUWARSA';
    position: absolute; inset: 0; background: rgba(15,42,94,.9);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; letter-spacing: 2px; font-size: .9rem;
    border-radius: 11px;
}
.qris-loader {
    display: flex; align-items: center; justify-content: center;
    flex-direction: column; gap: 10px; color: #94a3b8; padding: 10px;
    font-size: .78rem; text-align: center;
}
.qris-error-inner {
    display: flex; flex-direction: column; align-items: center;
    gap: 8px; padding: 6px;
}
.qris-error-inner i { font-size: 2rem; color: #ef4444; }
.qris-error-inner p { font-size: .7rem; color: #ef4444; text-align: center; line-height: 1.4; }

.qris-timer {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin: 0 16px 10px;
    padding: 9px 16px;
    background: #fef3c7; color: #92400e;
    border-radius: 10px; font-weight: 800;
    font-family: 'Courier New', monospace; font-size: 1rem;
    border: 1.5px solid #fcd34d;
}
.qris-timer.danger { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.qris-timer i { animation: pulse 1s ease-in-out infinite; }
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

.qris-info-row {
    display: flex; justify-content: space-between;
    font-size: .76rem; color: #94a3b8; padding: 4px 18px;
}
.qris-info-row b { color: #1e293b; font-family: 'Courier New', monospace; font-size: .75rem; }

.qris-status {
    margin: 8px 16px 0;
    padding: 10px 12px;
    background: #eff6ff; color: #2563eb;
    border-radius: 10px; font-size: .8rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.qris-status i { animation: spin 1.2s linear infinite; }

.qdlg-footer { padding: 12px 16px 18px; display: flex; flex-direction: column; gap: 8px; }

/* Success state */
.success-wrap { text-align: center; padding: 30px 16px; }
.success-icon {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, var(--success), #059669); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; margin: 0 auto 16px;
    animation: pop .5s ease-out;
}
@keyframes pop { 0% { transform: scale(0); } 70% { transform: scale(1.1); } 100% { transform: scale(1); } }
.success-wrap h2 { font-size: 1.3rem; font-weight: 800; color: var(--charcoal); margin-bottom: 6px; }
.success-wrap p  { font-size: .85rem; color: var(--gray-500); }
</style>
@endpush

@section('content')

<div class="page-header">
    <a href="{{ route('petugas.dashboard') }}" class="back"><i class="fa-solid fa-arrow-left"></i></a>
    <div>
        <h2>Kendaraan Keluar</h2>
        <p>Scan atau cari nomor polisi</p>
    </div>
</div>

<div id="mainArea">

<div class="tab-switcher">
    <button onclick="showTab('scan')" id="tab-scan" class="tab-btn active">
        <i class="fa-solid fa-camera"></i> Scan Kamera
    </button>
    <button onclick="showTab('manual')" id="tab-manual" class="tab-btn">
        <i class="fa-solid fa-keyboard"></i> Cari Manual
    </button>
</div>

{{-- SCAN TAB --}}
<div id="scanTab">
    <div class="scan-wrapper">
        <video id="videoScan" autoplay playsinline muted></video>
        <div class="scan-overlay">
            <div class="scan-frame">
                <div class="c-tr"></div><div class="c-bl"></div>
                <div class="scan-line"></div>
            </div>
        </div>
        <div class="scan-hint">Arahkan kamera ke plat nomor</div>
        <div class="processing-overlay" id="procOverlay">
            <div class="spinner"></div>
            <p id="procText">Memproses...</p>
        </div>
        <canvas id="canvasScan" style="display:none"></canvas>
    </div>

    <div class="cam-actions">
        <button class="cam-btn primary" onclick="captureAndRead()">
            <i class="fa-solid fa-camera"></i> Scan & Cari Transaksi
        </button>
        <button class="cam-btn ghost" onclick="toggleTorch()" id="torchBtn" title="Senter">
            <i class="fa-solid fa-bolt"></i>
        </button>
    </div>
</div>

{{-- MANUAL TAB --}}
<div id="manualTab" style="display:none">
    <div class="manual-search">
        <input type="text" id="manualInput" placeholder="cth: BA 1234 AB" maxlength="15" autocomplete="off">
        <button onclick="cariManual()"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
    <p style="font-size:.76rem;color:var(--gray-400);margin-bottom:8px;">Ketik nomor polisi yang ingin di-checkout.</p>
</div>

{{-- Loading --}}
<div id="loadingSection" class="section-card text-center" style="display:none;padding:30px;">
    <div class="spinner" style="margin:0 auto 12px;"></div>
    <p style="font-size:.85rem;color:var(--gray-500);" id="loadingText">Memproses...</p>
</div>

{{-- Result Section --}}
<div id="resultSection" style="display:none">
    <div id="trxInfo"></div>

    <div id="metodeSection" style="display:none">
        <div class="section-card">
            <div class="section-title">Pilih Metode Pembayaran</div>
            <div id="metodeList"></div>
        </div>

        <button onclick="confirmCheckout()" id="btnCheckout" class="btn btn-success btn-full" style="padding:14px;font-size:.95rem;" disabled>
            <i class="fa-solid fa-circle-check"></i> Proses Checkout
        </button>
        <button onclick="resetScan()" class="btn btn-secondary btn-full" style="margin-top:8px;">Batal</button>
    </div>

    <div id="notFoundSection" style="display:none" class="section-card text-center" style="padding:30px 16px;">
        <div style="font-size:3rem;margin-bottom:8px;">🔍</div>
        <p style="font-weight:700;color:var(--charcoal);margin-bottom:4px;">Transaksi Tidak Ditemukan</p>
        <p style="font-size:.78rem;color:var(--gray-500);">Nomor polisi tidak memiliki transaksi aktif</p>
        <button onclick="resetScan()" class="btn btn-primary" style="margin-top:14px;">Coba Lagi</button>
    </div>
</div>

</div>{{-- /mainArea --}}

{{-- QRIS Dialog --}}
<dialog id="qrisDialog">

    {{-- Header --}}
    <div class="qdlg-head">
        <div class="qdlg-brand"><i class="fa-solid fa-qrcode"></i> QRIS Bank Nagari</div>
        <button class="qdlg-close" onclick="cancelQris()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    {{-- Amount --}}
    <div class="qris-amount">
        <span class="l">Total Pembayaran</span>
        <span class="v" id="qrisAmount">Rp 0</span>
    </div>

    {{-- QR Image --}}
    <div class="qris-img" id="qrisImg">
        <div class="qris-loader">
            <div class="spinner"></div>
            <p>Menggenerate QR...</p>
        </div>
    </div>

    {{-- Timer --}}
    <div class="qris-timer" id="qrisTimer">
        <i class="fa-solid fa-clock"></i>
        <span>Berlaku</span>
        <span id="qrisTimerText" style="font-size:1.1rem;">05:00</span>
    </div>

    {{-- Info --}}
    <div class="qris-info-row">
        <span>Nomor Polisi</span><b id="qrisPlat">-</b>
    </div>
    <div class="qris-info-row" style="margin-bottom:8px;">
        <span>Billing ID</span><b id="qrisBilling">-</b>
    </div>

    {{-- Status --}}
    <div class="qris-status" id="qrisStatus">
        <i class="fa-solid fa-spinner"></i>
        <span>Menunggu pembayaran...</span>
    </div>

    {{-- Footer buttons --}}
    <div class="qdlg-footer">
        <button onclick="startQrisFlow()" id="qrisBtnRetry" class="btn btn-primary btn-full" style="display:none;">
            <i class="fa-solid fa-rotate-right"></i> Generate Ulang
        </button>
        <button onclick="cancelQris()" class="btn btn-secondary btn-full">
            <i class="fa-solid fa-xmark"></i> Batalkan
        </button>
    </div>
</dialog>

{{-- Confirm Dialog --}}
<dialog id="confirmDialog">
    <div style="padding:22px 18px 18px;">

        {{-- Icon + judul --}}
        <div style="text-align:center;margin-bottom:16px;">
            <div class="cdlg-icon" id="cdlgIcon" style="background:#fef3c7;color:#d97706;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div style="font-size:1rem;font-weight:800;color:#1e293b;">Konfirmasi Pembayaran</div>
            <div style="font-size:.76rem;color:#64748b;margin-top:3px;" id="confirmText">Pastikan data di bawah sudah benar</div>
        </div>

        {{-- Plat nomor --}}
        <div class="cdlg-plate">
            <div class="lbl">Nomor Polisi</div>
            <div class="val" id="cdlgPlat">-</div>
        </div>

        {{-- Detail rows --}}
        <div class="cdlg-rows">
            <div class="cdlg-row">
                <span class="rl"><i class="fa-solid fa-location-dot" style="color:#2563eb;"></i> Lokasi</span>
                <span class="rv" id="cdlgLokasi">-</span>
            </div>
            <div class="cdlg-row">
                <span class="rl"><i class="fa-solid fa-tag" style="color:#7c3aed;"></i> Tarif</span>
                <span class="rv" id="cdlgTarif">-</span>
            </div>
            <div class="cdlg-row">
                <span class="rl"><i class="fa-solid fa-clock" style="color:#d97706;"></i> Jam Masuk</span>
                <span class="rv" id="cdlgJam">-</span>
            </div>
            <div class="cdlg-row">
                <span class="rl"><i class="fa-solid fa-hourglass-half" style="color:#0891b2;"></i> Durasi</span>
                <span class="rv" id="cdlgDurasi">-</span>
            </div>
            <div class="cdlg-row">
                <span class="rl"><i class="fa-solid fa-wallet" style="color:#059669;"></i> Metode</span>
                <span class="rv" style="font-weight:800;" id="confirmMetode">-</span>
            </div>
        </div>

        {{-- Total --}}
        <div class="cdlg-total">
            <span class="tl"><i class="fa-solid fa-circle-check"></i> Total Bayar</span>
            <span class="tv" id="confirmTotal">-</span>
        </div>

        {{-- Tombol --}}
        <div class="cdlg-actions">
            <button onclick="document.getElementById('confirmDialog').close()" class="btn btn-secondary">
                Batal
            </button>
            <button onclick="afterConfirm()" class="btn btn-success" id="btnConfirmYa">
                <i class="fa-solid fa-circle-check"></i> Ya, Bayar Sekarang
            </button>
        </div>
    </div>
</dialog>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
let stream = null;
let torchOn = false;
let selectedMetode = null;
let selectedMetodeNama = '';
let currentTrxId = null;
let currentTotal = 0;
let currentTrxData = null;

const proc = document.getElementById('procOverlay');
const procText = document.getElementById('procText');
function showProc(t) { procText.textContent = t || 'Memproses...'; proc.classList.add('show'); }
function hideProc() { proc.classList.remove('show'); }

// Camera ─────────────────────────────────
async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 } }
        });
        document.getElementById('videoScan').srcObject = stream;
    } catch(e) {
        console.warn('Kamera tidak tersedia:', e);
        document.querySelector('.scan-wrapper').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:10px;color:rgba(255,255,255,.6);padding:20px;text-align:center;">
                <i class="fa-solid fa-camera-slash" style="font-size:2.2rem;"></i>
                <p style="font-size:.82rem;">Kamera tidak tersedia.<br>Gunakan tab "Cari Manual".</p>
            </div>`;
    }
}
function stopCamera() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
}
async function toggleTorch() {
    if (!stream) return;
    const track = stream.getVideoTracks()[0];
    try {
        torchOn = !torchOn;
        await track.applyConstraints({ advanced: [{ torch: torchOn }] });
        const b = document.getElementById('torchBtn');
        b.style.background = torchOn ? '#fef3c7' : '';
        b.style.color = torchOn ? '#d97706' : '';
    } catch(e) { alert('Senter tidak didukung'); }
}

function cropAndUpscale(src) {
    const w = src.width, h = src.height;
    const cw = Math.floor(w * 0.78), ch = Math.floor(h * 0.38);
    const cx = Math.floor((w - cw) / 2), cy = Math.floor((h - ch) / 2);
    const out = document.createElement('canvas');
    out.width = cw * 2; out.height = ch * 2;
    const ctx = out.getContext('2d');
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';
    ctx.drawImage(src, cx, cy, cw, ch, 0, 0, out.width, out.height);
    return out;
}

async function recognizePlate(canvasEl) {
    const dataUrl = canvasEl.toDataURL('image/jpeg', 0.85);
    const res = await fetch('{{ route('petugas.scan-plate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ image: dataUrl })
    });
    if (!res.ok) throw new Error('OCR HTTP ' + res.status);
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'OCR error');
    return { plate: data.plate, raw: data.raw };
}

async function captureAndRead() {
    if (!stream) { alert('Kamera belum siap'); return; }
    const video = document.getElementById('videoScan');
    const canvas = document.getElementById('canvasScan');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    showProc('Mengirim ke server OCR...');
    try {
        const prepped = cropAndUpscale(canvas);
        const { plate, raw } = await recognizePlate(prepped);
        hideProc();

        if (plate) {
            cariTransaksi(plate);
        } else {
            const guess = (raw || '').replace(/[^A-Z0-9 ]/gi, ' ').replace(/\s+/g, ' ').toUpperCase().trim();
            if (guess && confirm('Plat nomor tidak dikenali. Teks terbaca:\n\n"' + guess + '"\n\nGunakan ini untuk cari?')) {
                cariTransaksi(guess);
            } else {
                alert('Plat tidak terbaca. Coba ulangi scan atau pakai tab "Cari Manual".');
            }
        }
    } catch(e) {
        console.error(e);
        hideProc();
        alert('Gagal scan: ' + e.message + '\nGunakan pencarian manual.');
    }
}

// Tabs ───────────────────────────────────
function showTab(tab) {
    const isScan = tab === 'scan';
    document.getElementById('scanTab').style.display = isScan ? '' : 'none';
    document.getElementById('manualTab').style.display = isScan ? 'none' : '';
    document.getElementById('tab-scan').classList.toggle('active', isScan);
    document.getElementById('tab-manual').classList.toggle('active', !isScan);
    if (isScan && !stream) startCamera();
    if (!isScan) stopCamera();
    if (!isScan) setTimeout(() => document.getElementById('manualInput').focus(), 100);
}

function cariManual() {
    const v = document.getElementById('manualInput').value.trim().toUpperCase();
    if (!v) { alert('Masukkan nomor polisi'); return; }
    cariTransaksi(v);
}

// Search ─────────────────────────────────
async function cariTransaksi(plate) {
    document.getElementById('loadingSection').style.display = '';
    document.getElementById('loadingText').textContent = 'Mencari transaksi...';
    document.getElementById('resultSection').style.display = 'none';
    try {
        const res = await fetch(`{{ route('petugas.cari-checkout') }}?nomor_polisi=${encodeURIComponent(plate)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        document.getElementById('loadingSection').style.display = 'none';
        document.getElementById('resultSection').style.display = '';
        document.getElementById('trxInfo').innerHTML = '';
        document.getElementById('metodeSection').style.display = 'none';
        document.getElementById('notFoundSection').style.display = 'none';

        if (data.found && data.transaksi) {
            renderTrxInfo(data.transaksi);
            renderMetode(data.metode);
            currentTrxId   = data.transaksi.id;
            currentTotal   = data.transaksi.tarif_harga;
            currentTrxData = data.transaksi;
        } else {
            document.getElementById('notFoundSection').style.display = '';
        }
    } catch(e) {
        document.getElementById('loadingSection').style.display = 'none';
        document.getElementById('resultSection').style.display = '';
        document.getElementById('notFoundSection').style.display = '';
    }
}

function renderTrxInfo(trx) {
    document.getElementById('trxInfo').innerHTML = `
        <div class="trx-card">
            <div class="trx-card-header">
                <div>
                    <div class="ref">No. Referensi</div>
                    <div class="ref-val">${trx.nomor_referensi}</div>
                </div>
                <span class="badge badge-warning" style="padding:5px 10px;">PARKIR</span>
            </div>
            <div class="trx-plate">
                <div class="lbl">Nomor Polisi</div>
                <div class="val">${trx.nomor_polisi}</div>
            </div>
            <div class="trx-row"><span class="trx-label"><i class="fa-solid fa-location-dot text-primary"></i> Lokasi</span><span class="trx-value">${trx.lokasi}</span></div>
            <div class="trx-row"><span class="trx-label"><i class="fa-solid fa-tag text-primary"></i> Tarif</span><span class="trx-value">${trx.tarif_nama}</span></div>
            <div class="trx-row"><span class="trx-label"><i class="fa-solid fa-clock text-primary"></i> Jam Masuk</span><span class="trx-value">${trx.jam_masuk}</span></div>
            <div class="trx-row"><span class="trx-label"><i class="fa-solid fa-hourglass-half text-primary"></i> Durasi</span><span class="trx-value text-warning">${trx.durasi}</span></div>
            <div class="trx-row total"><span class="trx-label">Total Biaya</span><span class="trx-value">Rp ${Number(trx.tarif_harga).toLocaleString('id-ID')}</span></div>
        </div>`;
    document.getElementById('metodeSection').style.display = '';
}

function renderMetode(metodes) {
    const icons = { 'cash': '💵', 'qris': '📱', 'transfer': '🏦', 'debit': '💳' };
    const colors = { 'cash': '#dcfce7', 'qris': '#dbeafe', 'transfer': '#fef3c7', 'debit': '#e0f2fe' };
    let html = '';
    metodes.forEach(m => {
        const key = m.nama.toLowerCase();
        const icon = Object.entries(icons).find(([k]) => key.includes(k))?.[1] || '💰';
        const color = Object.entries(colors).find(([k]) => key.includes(k))?.[1] || '#f1f5f9';
        const desc = key.includes('qris') ? 'Bayar pakai scan QR'
                  : key.includes('cash') ? 'Bayar tunai langsung'
                  : 'Metode pembayaran';
        html += `
        <button type="button" class="metode-btn" onclick="pilihMetode(${m.id}, '${m.nama.replace(/'/g,"\\'")}', this)" data-id="${m.id}">
            <div class="metode-icon" style="background:${color}">${icon}</div>
            <div class="metode-info">
                <div class="nm">${m.nama}</div>
                <div class="ds">${desc}</div>
            </div>
            <div class="metode-check"><i class="fa-solid fa-check" style="font-size:.7rem;display:none;"></i></div>
        </button>`;
    });
    document.getElementById('metodeList').innerHTML = html;
}

function pilihMetode(id, nama, btn) {
    document.querySelectorAll('.metode-btn').forEach(b => {
        b.classList.remove('selected');
        b.querySelector('.metode-check i').style.display = 'none';
    });
    btn.classList.add('selected');
    btn.querySelector('.metode-check i').style.display = '';
    selectedMetode = id;
    selectedMetodeNama = nama;
    document.getElementById('btnCheckout').disabled = false;
}

// Konfirmasi sebelum checkout ────────────
function confirmCheckout() {
    if (!selectedMetode) { alert('Pilih metode pembayaran dahulu'); return; }

    const isQris = selectedMetodeNama.toLowerCase().includes('qris');

    // Isi icon sesuai metode
    const iconEl = document.getElementById('cdlgIcon');
    if (isQris) {
        iconEl.style.background = '#dbeafe'; iconEl.style.color = '#2563eb';
        iconEl.innerHTML = '<i class="fa-solid fa-qrcode"></i>';
    } else {
        iconEl.style.background = '#dcfce7'; iconEl.style.color = '#059669';
        iconEl.innerHTML = '<i class="fa-solid fa-money-bill-wave"></i>';
    }

    // Isi detail dari currentTrxData
    const t = currentTrxData || {};
    document.getElementById('cdlgPlat').textContent    = t.nomor_polisi || '-';
    document.getElementById('cdlgLokasi').textContent  = t.lokasi || '-';
    document.getElementById('cdlgTarif').textContent   = t.tarif_nama || '-';
    document.getElementById('cdlgJam').textContent     = t.jam_masuk || '-';
    document.getElementById('cdlgDurasi').textContent  = t.durasi || '-';
    document.getElementById('confirmMetode').textContent = selectedMetodeNama;
    document.getElementById('confirmTotal').textContent  = 'Rp ' + Number(currentTotal).toLocaleString('id-ID');
    document.getElementById('confirmText').textContent   = isQris
        ? 'Tampilkan QR Code QRIS untuk dibayar'
        : 'Pastikan pembayaran tunai sudah diterima';

    document.getElementById('confirmDialog').showModal();
}

function afterConfirm() {
    document.getElementById('confirmDialog').close();
    if (selectedMetodeNama.toLowerCase().includes('qris')) {
        startQrisFlow();
    } else {
        prosesCheckoutCash();
    }
}

// ─── QRIS Flow (Bank Nagari real API + polling) ──────────────
let qrisTimerInterval = null;
let qrisPollInterval = null;
let qrisExpiresAt = null;

function qrisShowLoading() {
    document.getElementById('qrisImg').classList.remove('expired');
    document.getElementById('qrisImg').innerHTML =
        '<div class="qris-loader"><div class="spinner"></div><p>Menghubungi Bank Nagari...</p></div>';
    document.getElementById('qrisTimerText').textContent = '05:00';
    document.getElementById('qrisTimer').classList.remove('danger');
    document.getElementById('qrisTimer').style.display = '';
    document.getElementById('qrisStatus').innerHTML =
        '<i class="fa-solid fa-spinner"></i><span>Menunggu pembayaran...</span>';
    document.getElementById('qrisStatus').style.cssText = '';
    document.getElementById('qrisBtnRetry').style.display = 'none';
}

function qrisShowError(msg) {
    if (qrisTimerInterval) { clearInterval(qrisTimerInterval); qrisTimerInterval = null; }
    if (qrisPollInterval)  { clearInterval(qrisPollInterval);  qrisPollInterval  = null; }

    document.getElementById('qrisImg').innerHTML = `
        <div class="qris-error-inner">
            <i class="fa-solid fa-circle-exclamation"></i>
            <p>${msg}</p>
        </div>`;
    document.getElementById('qrisTimer').style.display = 'none';
    document.getElementById('qrisStatus').style.background = '#fee2e2';
    document.getElementById('qrisStatus').style.color = '#991b1b';
    document.getElementById('qrisStatus').innerHTML =
        '<i class="fa-solid fa-triangle-exclamation" style="animation:none;"></i><span>Gagal generate QR</span>';
    document.getElementById('qrisBtnRetry').style.display = '';
}

async function startQrisFlow() {
    if (!currentTrxId) return;

    // Reset UI & buka dialog
    document.getElementById('qrisAmount').textContent = 'Rp ' + Number(currentTotal).toLocaleString('id-ID');
    document.getElementById('qrisBilling').textContent = currentTrxData?.billing_number || '-';
    document.getElementById('qrisPlat').textContent = currentTrxData?.nomor_polisi || '-';
    qrisShowLoading();

    if (!document.getElementById('qrisDialog').open) {
        document.getElementById('qrisDialog').showModal();
    }

    try {
        const res = await fetch(`{{ url('/petugas/qris/generate') }}/${currentTrxId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();

        if (!data.success) {
            qrisShowError(data.message || 'Gagal generate QR dari server');
            return;
        }

        renderQr(data.qr_string, data.qr_type || 'qr_string');
        document.getElementById('qrisBilling').textContent = data.billing_number;
        qrisExpiresAt = new Date(data.expires_at).getTime();

        startQrisCountdown();
        startQrisPolling(data.status_url, data.sukses_url);

    } catch(e) {
        qrisShowError('Tidak dapat menghubungi server.<br>' + e.message);
    }
}

function renderQr(qrString, qrType) {
    const container = document.getElementById('qrisImg');
    container.classList.remove('expired');
    container.innerHTML = '';

    if (qrType === 'base64_png') {
        // Bank Nagari mengembalikan base64 PNG langsung
        const img = document.createElement('img');
        img.src = 'data:image/png;base64,' + qrString;
        img.style.cssText = 'width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;';
        img.onerror = () => {
            container.innerHTML = '<div class="qris-loader" style="color:#ef4444;"><i class="fa-solid fa-circle-exclamation" style="font-size:1.5rem;"></i><p>Gagal render gambar QR</p></div>';
        };
        container.appendChild(img);
    } else {
        // QR string teks biasa — generate dengan QRCode.js
        QRCode.toCanvas(qrString, {
            width: 220, margin: 1, errorCorrectionLevel: 'M',
            color: { dark: '#0f2a5e', light: '#ffffff' }
        }, function(err, c) {
            if (err) {
                container.innerHTML = '<div class="qris-loader" style="color:#ef4444;">Gagal render QR</div>';
                return;
            }
            container.appendChild(c);
        });
    }
}

function startQrisCountdown() {
    if (qrisTimerInterval) clearInterval(qrisTimerInterval);
    qrisTimerInterval = setInterval(() => {
        const remaining = Math.max(0, qrisExpiresAt - Date.now());
        const m = Math.floor(remaining / 60000);
        const s = Math.floor((remaining % 60000) / 1000);
        document.getElementById('qrisTimerText').textContent =
            String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');

        if (remaining <= 60000) document.getElementById('qrisTimer').classList.add('danger');

        if (remaining <= 0) {
            clearInterval(qrisTimerInterval);
            qrisExpired();
        }
    }, 250);
}

function qrisExpired() {
    document.getElementById('qrisImg').classList.add('expired');
    document.getElementById('qrisStatus').innerHTML = '<i class="fa-solid fa-clock"></i><span>QR Code kedaluwarsa. Silakan ulangi.</span>';
    document.getElementById('qrisStatus').style.background = 'var(--danger-soft)';
    document.getElementById('qrisStatus').style.color = 'var(--danger)';
    if (qrisPollInterval) { clearInterval(qrisPollInterval); qrisPollInterval = null; }
}

function startQrisPolling(statusUrl, suksesUrl) {
    if (qrisPollInterval) clearInterval(qrisPollInterval);

    let polling = false;
    qrisPollInterval = setInterval(async () => {
        if (polling) return;
        polling = true;
        try {
            const r = await fetch(statusUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const d = await r.json();
            if (d.success && d.lunas) {
                clearInterval(qrisPollInterval);
                clearInterval(qrisTimerInterval);
                qrisPollInterval = null;
                // Tampilkan animasi success singkat lalu redirect
                document.getElementById('qrisStatus').innerHTML = '<i class="fa-solid fa-circle-check" style="animation:none;color:var(--success)"></i><span style="color:var(--success);">Pembayaran diterima! Mengarahkan...</span>';
                document.getElementById('qrisStatus').style.background = 'var(--success-soft)';
                stopCamera();
                setTimeout(() => { window.location.href = suksesUrl; }, 900);
            }
        } catch(e) { /* abaikan, lanjut polling */ }
        polling = false;
    }, 3000); // poll tiap 3 detik
}

function cancelQris() {
    if (qrisTimerInterval) clearInterval(qrisTimerInterval);
    if (qrisPollInterval)  clearInterval(qrisPollInterval);
    qrisTimerInterval = qrisPollInterval = null;
    document.getElementById('qrisDialog').close();
}

// ─── Cash flow ─────────────────────────────
async function prosesCheckoutCash() {
    if (!selectedMetode || !currentTrxId) return;
    const btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

    try {
        const res = await fetch(`{{ url('/petugas/checkout') }}/${currentTrxId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id_metode_pembayaran: selectedMetode })
        });
        const data = await res.json();
        if (data.success && data.redirect) {
            stopCamera();
            window.location.href = data.redirect;
        } else if (data.success) {
            stopCamera();
            window.location.href = '{{ url('/petugas/dashboard') }}';
        } else {
            alert(data.message || 'Gagal proses');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Proses Checkout';
        }
    } catch(e) {
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Proses Checkout';
    }
}

function resetScan() {
    document.getElementById('resultSection').style.display = 'none';
    currentTrxId = null; selectedMetode = null; selectedMetodeNama = ''; currentTotal = 0; currentTrxData = null;
    const mi = document.getElementById('manualInput'); if (mi) mi.value = '';
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    startCamera();

    // Auto-fill jika ada ?plate=... di URL
    const params = new URLSearchParams(window.location.search);
    const prefill = params.get('plate');
    if (prefill) {
        showTab('manual');
        document.getElementById('manualInput').value = prefill.toUpperCase();
        setTimeout(() => cariTransaksi(prefill.toUpperCase()), 200);
    }

    document.getElementById('manualInput').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    document.getElementById('manualInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); cariManual(); }
    });
});

window.addEventListener('pagehide', stopCamera);
</script>
@endpush
