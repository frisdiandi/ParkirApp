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
    position: relative; background: #000; border-radius: 16px;
    overflow: hidden; aspect-ratio: 4/3; max-height: 280px;
    margin-bottom: 12px;
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

/* QRIS modal */
.qris-card {
    background: var(--white); border-radius: 16px; padding: 22px 18px;
    text-align: center;
}
.qris-card .qhead { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.qris-brand {
    display: inline-flex; align-items: center; gap: 7px;
    color: var(--white); background: linear-gradient(135deg, #cd1f2c, #781923);
    padding: 6px 12px; border-radius: 8px; font-weight: 800; font-size: .82rem; letter-spacing: .5px;
}
.qris-img {
    width: 200px; height: 200px; margin: 8px auto;
    background: #fff; border: 4px solid var(--navy); border-radius: 12px;
    padding: 8px; display: flex; align-items: center; justify-content: center;
}
.qris-img svg { width: 100%; height: 100%; }
.qris-amount {
    background: var(--gray-50); border-radius: 12px;
    padding: 10px 16px; margin: 14px 0 8px;
}
.qris-amount .l { font-size: .68rem; color: var(--gray-500); font-weight: 700; text-transform: uppercase; }
.qris-amount .v { font-size: 1.5rem; font-weight: 800; color: var(--navy); margin-top: 4px; }

/* Success state */
.success-wrap {
    text-align: center; padding: 30px 16px;
}
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
    <div class="qris-card">
        <div class="qhead">
            <div class="qris-brand">QRIS</div>
            <button onclick="document.getElementById('qrisDialog').close()" style="border:none;background:var(--gray-100);border-radius:10px;width:32px;height:32px;cursor:pointer;color:var(--gray-500);">✕</button>
        </div>
        <p style="font-size:.78rem;color:var(--gray-500);margin-bottom:4px;">Scan QR Code di bawah ini</p>
        <p style="font-size:.65rem;color:var(--warning);font-weight:700;">⚠ DEMO / DUMMY QR — Bukan transaksi nyata</p>
        <div class="qris-img" id="qrisImg"></div>
        <div class="qris-amount">
            <div class="l">Total Pembayaran</div>
            <div class="v" id="qrisAmount">Rp 0</div>
        </div>
        <button onclick="confirmQrisPaid()" class="btn btn-success btn-full" style="margin-top:6px;padding:13px;">
            <i class="fa-solid fa-check"></i> Konfirmasi Sudah Dibayar
        </button>
        <button onclick="document.getElementById('qrisDialog').close()" class="btn btn-secondary btn-full" style="margin-top:8px;">Batal</button>
    </div>
</dialog>

{{-- Confirm Dialog --}}
<dialog id="confirmDialog">
    <div style="padding:22px 20px;text-align:center;">
        <div style="width:60px;height:60px;border-radius:50%;background:var(--warning-soft);color:var(--warning);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 14px;">
            <i class="fa-solid fa-question"></i>
        </div>
        <h3 style="font-size:1.05rem;font-weight:800;color:var(--charcoal);margin-bottom:6px;">Konfirmasi Checkout</h3>
        <p style="font-size:.85rem;color:var(--gray-500);margin-bottom:6px;" id="confirmText">Lanjutkan proses pembayaran?</p>
        <div style="background:var(--gray-50);border-radius:12px;padding:10px 14px;margin:14px 0;">
            <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:4px;">
                <span style="color:var(--gray-500);">Metode</span>
                <span style="font-weight:700;color:var(--charcoal);" id="confirmMetode">-</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                <span style="color:var(--gray-500);">Total</span>
                <span style="font-weight:800;color:var(--success);" id="confirmTotal">-</span>
            </div>
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="document.getElementById('confirmDialog').close()" class="btn btn-secondary" style="flex:1;">Batal</button>
            <button onclick="afterConfirm()" class="btn btn-success" style="flex:1;" id="btnConfirmYa">Ya, Lanjutkan</button>
        </div>
    </div>
</dialog>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.1.1/tesseract.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
let stream = null;
let torchOn = false;
let selectedMetode = null;
let selectedMetodeNama = '';
let currentTrxId = null;
let currentTotal = 0;

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

function preprocess(c) {
    const ctx = c.getContext('2d');
    const img = ctx.getImageData(0, 0, c.width, c.height);
    const d = img.data;
    for (let i = 0; i < d.length; i += 4) {
        const g = (d[i] * 0.299 + d[i+1] * 0.587 + d[i+2] * 0.114);
        const v = g > 130 ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
    }
    ctx.putImageData(img, 0, 0);
    return c;
}
function cropArea(src) {
    const w = src.width, h = src.height;
    const cw = Math.floor(w * 0.78), ch = Math.floor(h * 0.38);
    const cx = Math.floor((w - cw) / 2), cy = Math.floor((h - ch) / 2);
    const out = document.createElement('canvas');
    out.width = cw; out.height = ch;
    out.getContext('2d').drawImage(src, cx, cy, cw, ch, 0, 0, cw, ch);
    return out;
}

async function captureAndRead() {
    if (!stream) { alert('Kamera belum siap'); return; }
    const video = document.getElementById('videoScan');
    const canvas = document.getElementById('canvasScan');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    showProc('Membaca plat nomor...');
    try {
        const cropped = cropArea(canvas);
        const prepped = preprocess(cropped);
        const { data: { text } } = await Tesseract.recognize(prepped, 'eng', {
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            tessedit_pageseg_mode: '7'
        });
        const cleaned = text.replace(/[^A-Z0-9 ]/gi, ' ').replace(/\s+/g,' ').trim().toUpperCase();
        const match = cleaned.match(/[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{0,3}/);
        let plate = match ? match[0].replace(/([A-Z]+)(\d+)([A-Z]*)/, '$1 $2 $3').replace(/\s+/g,' ').trim() : cleaned.substring(0, 12).trim();
        hideProc();
        if (plate && plate.length >= 4) cariTransaksi(plate);
        else alert('Plat nomor tidak terbaca. Coba lagi atau pakai pencarian manual.');
    } catch(e) {
        console.error(e);
        hideProc();
        alert('Gagal membaca. Gunakan pencarian manual.');
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
            currentTrxId = data.transaksi.id;
            currentTotal = data.transaksi.tarif_harga;
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

    document.getElementById('confirmMetode').textContent = selectedMetodeNama;
    document.getElementById('confirmTotal').textContent = 'Rp ' + Number(currentTotal).toLocaleString('id-ID');
    document.getElementById('confirmText').textContent =
        selectedMetodeNama.toLowerCase().includes('qris')
            ? 'Tampilkan QR Code QRIS untuk pembayaran?'
            : 'Pastikan pembayaran tunai sudah diterima.';
    document.getElementById('confirmDialog').showModal();
}

function afterConfirm() {
    document.getElementById('confirmDialog').close();
    if (selectedMetodeNama.toLowerCase().includes('qris')) {
        showQris();
    } else {
        prosesCheckoutFinal();
    }
}

// QRIS dummy ─────────────────────────────
function showQris() {
    document.getElementById('qrisAmount').textContent = 'Rp ' + Number(currentTotal).toLocaleString('id-ID');
    const payload = `DUMMY|SIRP|TRX${currentTrxId}|${currentTotal}|${Date.now()}`;

    const container = document.getElementById('qrisImg');
    container.innerHTML = '';
    QRCode.toCanvas(payload, { width: 200, margin: 1, color: { dark: '#0f2a5e', light: '#ffffff' } }, function(err, c) {
        if (err) { container.textContent = 'Gagal generate QR'; return; }
        container.appendChild(c);
    });
    document.getElementById('qrisDialog').showModal();
}
function confirmQrisPaid() {
    document.getElementById('qrisDialog').close();
    prosesCheckoutFinal();
}

async function prosesCheckoutFinal() {
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
        if (data.success) showSuccess(data);
        else { alert(data.message || 'Gagal proses'); btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Proses Checkout'; }
    } catch(e) {
        alert('Terjadi kesalahan jaringan');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Proses Checkout';
    }
}

function showSuccess(data) {
    stopCamera();
    document.getElementById('mainArea').innerHTML = `
        <div class="success-wrap">
            <div class="success-icon"><i class="fa-solid fa-check"></i></div>
            <h2>Checkout Berhasil!</h2>
            <p>Kendaraan ${data.nomor_polisi || ''} telah keluar</p>
            <div class="trx-card" style="margin:20px 0;text-align:left;">
                <div class="trx-row"><span class="trx-label">No. Referensi</span><span class="trx-value">${data.nomor_referensi || '-'}</span></div>
                <div class="trx-row"><span class="trx-label">Nomor Polisi</span><span class="trx-value">${data.nomor_polisi || '-'}</span></div>
                <div class="trx-row"><span class="trx-label">Durasi</span><span class="trx-value">${data.durasi || '-'}</span></div>
                <div class="trx-row"><span class="trx-label">Metode Bayar</span><span class="trx-value">${data.metode_nama || '-'}</span></div>
                <div class="trx-row total"><span class="trx-label">Total Dibayar</span><span class="trx-value">Rp ${Number(data.total || 0).toLocaleString('id-ID')}</span></div>
            </div>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-primary btn-full" style="padding:14px;">
                <i class="fa-solid fa-house"></i> Kembali ke Dashboard
            </a>
            <a href="{{ route('petugas.scan-checkout') }}" class="btn btn-secondary btn-full" style="margin-top:8px;">
                Checkout Lain
            </a>
        </div>`;
}

function resetScan() {
    document.getElementById('resultSection').style.display = 'none';
    currentTrxId = null; selectedMetode = null; selectedMetodeNama = ''; currentTotal = 0;
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
