@extends('layouts.petugas')

@section('title', 'Scan Checkout')

@push('styles')
<style>
.scan-wrapper {
    position: relative;
    background: #000;
    border-radius: 16px;
    overflow: hidden;
    aspect-ratio: 4/3;
    max-height: 260px;
}
#videoScan {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.scan-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.scan-frame {
    width: 78%;
    height: 44%;
    border: 3px solid #3b82f6;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
}
.scan-line {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, #3b82f6, #60a5fa, #3b82f6, transparent);
    animation: scan 2s linear infinite;
    border-radius: 2px;
}
@keyframes scan {
    0%   { top: 0; }
    50%  { top: calc(100% - 3px); }
    100% { top: 0; }
}
.corner { position: absolute; width: 18px; height: 18px; }
.corner-tl { top: -3px; left: -3px; border-top: 4px solid #60a5fa; border-left: 4px solid #60a5fa; border-radius: 4px 0 0 0; }
.corner-tr { top: -3px; right: -3px; border-top: 4px solid #60a5fa; border-right: 4px solid #60a5fa; border-radius: 0 4px 0 0; }
.corner-bl { bottom: -3px; left: -3px; border-bottom: 4px solid #60a5fa; border-left: 4px solid #60a5fa; border-radius: 0 0 0 4px; }
.corner-br { bottom: -3px; right: -3px; border-bottom: 4px solid #60a5fa; border-right: 4px solid #60a5fa; border-radius: 0 0 4px 0; }
.hint-text { font-size: 0.7rem; color: rgba(255,255,255,0.9); margin-top: 6px; text-align: center; }

.plate-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: #1e3a5f; color: #fff;
    padding: 10px 18px; border-radius: 30px;
    font-size: 1.2rem; font-weight: 700; letter-spacing: 2px;
    font-family: 'Courier New', monospace;
}

.trx-card {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
}
.trx-card-header {
    background: linear-gradient(135deg, #1e3a5f, #2563eb);
    padding: 14px 16px;
    color: #fff;
}
.trx-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
}
.trx-row:last-child { border-bottom: none; }
.trx-label { color: #64748b; }
.trx-value { font-weight: 600; color: #1e293b; }

.metode-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    margin-bottom: 10px;
}
.metode-btn.selected { border-color: #2563eb; background: #eff6ff; }
.metode-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

.section-card {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    padding: 16px;
    margin-bottom: 14px;
}
.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}

.manual-search {
    display: flex; gap: 8px;
    margin-bottom: 14px;
}
.manual-search input {
    flex: 1;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.9rem;
    outline: none;
    text-transform: uppercase;
}
.manual-search input:focus { border-color: #2563eb; }
.manual-search button {
    padding: 10px 16px;
    background: #2563eb; color: #fff;
    border: none; border-radius: 10px;
    font-weight: 600; cursor: pointer;
}

#resultSection { display: none; }
#loadingSection { display: none; }
.spinner { width: 36px; height: 36px; border: 4px solid #e2e8f0; border-top-color: #2563eb; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="px-4 pt-2 pb-28">

    {{-- Page Title --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('petugas.dashboard') }}" class="w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-slate-800">Scan Checkout</h1>
            <p class="text-xs text-slate-500">Scan atau cari nomor polisi</p>
        </div>
    </div>

    {{-- Tab --}}
    <div class="flex bg-slate-100 rounded-xl p-1 mb-4">
        <button onclick="showTab('scan')" id="tab-scan" class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all bg-white text-blue-700 shadow-sm">
            📷 Scan Kamera
        </button>
        <button onclick="showTab('manual')" id="tab-manual" class="flex-1 py-2 text-sm font-semibold rounded-lg transition-all text-slate-500">
            ⌨️ Ketik Manual
        </button>
    </div>

    {{-- SCAN TAB --}}
    <div id="scanTab">
        <div class="scan-wrapper mb-2">
            <video id="videoScan" autoplay playsinline muted></video>
            <div class="scan-overlay">
                <div>
                    <div class="scan-frame">
                        <div class="scan-line"></div>
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                    </div>
                    <p class="hint-text">Arahkan kamera ke nomor polisi</p>
                </div>
            </div>
        </div>
        <div class="flex gap-2 mb-4">
            <button onclick="captureAndRead()" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 active:scale-95 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/><path d="M20.94 11A9 9 0 1 1 12 3"/>
                </svg>
                Ambil Foto & Scan
            </button>
            <button onclick="toggleTorch()" id="torchBtn" class="w-12 h-12 bg-slate-700 text-white rounded-xl flex items-center justify-center text-lg">
                🔦
            </button>
        </div>
        <canvas id="canvasScan" style="display:none"></canvas>
    </div>

    {{-- MANUAL TAB --}}
    <div id="manualTab" style="display:none">
        <div class="manual-search">
            <input type="text" id="manualInput" placeholder="Contoh: B 1234 ABC" maxlength="12">
            <button onclick="cariManual()">Cari</button>
        </div>
    </div>

    {{-- Loading --}}
    <div id="loadingSection" class="section-card text-center py-6">
        <div class="spinner"></div>
        <p class="text-sm text-slate-500" id="loadingText">Memproses...</p>
    </div>

    {{-- Result Section --}}
    <div id="resultSection">

        {{-- Detected Plate --}}
        <div class="section-card">
            <div class="section-title">Nomor Polisi Terdeteksi</div>
            <div class="flex items-center justify-between">
                <div class="plate-chip">
                    🚗 <span id="detectedPlate">-</span>
                </div>
                <button onclick="resetScan()" class="text-xs text-blue-600 font-semibold underline">Ganti</button>
            </div>
        </div>

        {{-- Transaksi Info --}}
        <div id="trxInfo">
            {{-- diisi via JS --}}
        </div>

        {{-- Metode Pembayaran --}}
        <div id="metodeSection" style="display:none">
            <div class="section-card">
                <div class="section-title">Pilih Metode Pembayaran</div>
                <div id="metodeList"></div>
            </div>

            <button onclick="prosesCheckout()" id="btnCheckout"
                class="w-full py-4 bg-green-600 text-white rounded-xl font-bold text-base flex items-center justify-center gap-2 active:scale-95 transition-transform shadow-lg">
                ✅ Proses Checkout & Bayar
            </button>
        </div>

        {{-- Not Found --}}
        <div id="notFoundSection" style="display:none" class="section-card text-center py-6">
            <div class="text-4xl mb-3">🔍</div>
            <p class="font-semibold text-slate-700">Transaksi Tidak Ditemukan</p>
            <p class="text-xs text-slate-500 mt-1">Nomor polisi tidak memiliki transaksi aktif</p>
            <button onclick="resetScan()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">Coba Lagi</button>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.1.1/tesseract.min.js"></script>
<script>
let stream = null;
let torchOn = false;
let selectedMetode = null;
let currentTrxId = null;

// ─── Camera ───────────────────────────────────────────
async function startCamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1280 } }
        });
        document.getElementById('videoScan').srcObject = stream;
    } catch(e) {
        console.warn('Kamera tidak tersedia:', e);
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
        document.getElementById('torchBtn').textContent = torchOn ? '💡' : '🔦';
    } catch(e) {}
}

async function captureAndRead() {
    const video = document.getElementById('videoScan');
    const canvas = document.getElementById('canvasScan');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    showLoading('Membaca nomor polisi...');

    try {
        const result = await Tesseract.recognize(canvas, 'eng', {
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 '
        });
        let raw = result.data.text.replace(/[^A-Z0-9 ]/gi, '').trim().toUpperCase();
        // simple plate pattern match
        const match = raw.match(/[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{1,3}/);
        const plate = match ? match[0].replace(/\s+/g,' ').trim() : raw.substring(0,10);
        hideLoading();
        if (plate) cariTransaksi(plate);
        else { hideLoading(); alert('Nomor polisi tidak terbaca. Coba lagi.'); }
    } catch(e) {
        hideLoading();
        alert('Gagal membaca. Coba ketik manual.');
    }
}

// ─── Tabs ──────────────────────────────────────────────
function showTab(tab) {
    const isScan = tab === 'scan';
    document.getElementById('scanTab').style.display = isScan ? '' : 'none';
    document.getElementById('manualTab').style.display = isScan ? 'none' : '';
    document.getElementById('tab-scan').className = 'flex-1 py-2 text-sm font-semibold rounded-lg transition-all ' + (isScan ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500');
    document.getElementById('tab-manual').className = 'flex-1 py-2 text-sm font-semibold rounded-lg transition-all ' + (!isScan ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-500');
    if (isScan && !stream) startCamera();
    if (!isScan) stopCamera();
}

function cariManual() {
    const val = document.getElementById('manualInput').value.trim().toUpperCase();
    if (!val) { alert('Masukkan nomor polisi'); return; }
    cariTransaksi(val);
}

// ─── Search Transaksi ──────────────────────────────────
async function cariTransaksi(plate) {
    showLoading('Mencari transaksi...');
    document.getElementById('resultSection').style.display = '';
    document.getElementById('detectedPlate').textContent = plate;
    document.getElementById('trxInfo').innerHTML = '';
    document.getElementById('metodeSection').style.display = 'none';
    document.getElementById('notFoundSection').style.display = 'none';

    try {
        const res = await fetch(`{{ route('petugas.cari-checkout') }}?nomor_polisi=${encodeURIComponent(plate)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        hideLoading();

        if (data.found && data.transaksi) {
            renderTrxInfo(data.transaksi);
            renderMetode(data.metode);
            currentTrxId = data.transaksi.id;
        } else {
            document.getElementById('notFoundSection').style.display = '';
        }
    } catch(e) {
        hideLoading();
        document.getElementById('notFoundSection').style.display = '';
    }
}

function renderTrxInfo(trx) {
    const dur = trx.durasi || '-';
    document.getElementById('trxInfo').innerHTML = `
        <div class="trx-card mb-3">
            <div class="trx-card-header">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs opacity-75">No. Referensi</p>
                        <p class="font-bold text-sm">${trx.nomor_referensi}</p>
                    </div>
                    <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">PARKIR</span>
                </div>
            </div>
            <div class="trx-row"><span class="trx-label">Lokasi</span><span class="trx-value">${trx.lokasi}</span></div>
            <div class="trx-row"><span class="trx-label">Tarif</span><span class="trx-value">${trx.tarif_nama}</span></div>
            <div class="trx-row"><span class="trx-label">Jam Masuk</span><span class="trx-value">${trx.jam_masuk}</span></div>
            <div class="trx-row"><span class="trx-label">Durasi</span><span class="trx-value text-orange-600">⏱ ${dur}</span></div>
            <div class="trx-row"><span class="trx-label">Biaya</span><span class="trx-value text-green-700 text-base">Rp ${Number(trx.tarif_harga).toLocaleString('id-ID')}</span></div>
        </div>`;
    document.getElementById('metodeSection').style.display = '';
}

function renderMetode(metodes) {
    const icons = { 'cash': '💵', 'qris': '📱', 'transfer': '🏦', 'debit': '💳' };
    const colors = { 'cash': '#dcfce7', 'qris': '#eff6ff', 'transfer': '#fef3c7', 'debit': '#f0f9ff' };
    let html = '';
    metodes.forEach(m => {
        const key = m.nama.toLowerCase();
        const icon = Object.entries(icons).find(([k]) => key.includes(k))?.[1] || '💰';
        const color = Object.entries(colors).find(([k]) => key.includes(k))?.[1] || '#f8fafc';
        html += `
        <button class="metode-btn" onclick="pilihMetode(${m.id}, this)" data-id="${m.id}">
            <div class="metode-icon" style="background:${color}">${icon}</div>
            <div class="flex-1 text-left">
                <div class="font-semibold text-slate-800 text-sm">${m.nama}</div>
                <div class="text-xs text-slate-500">Tap untuk memilih</div>
            </div>
            <div id="check-${m.id}" style="display:none">✅</div>
        </button>`;
    });
    document.getElementById('metodeList').innerHTML = html;
}

function pilihMetode(id, btn) {
    document.querySelectorAll('.metode-btn').forEach(b => {
        b.classList.remove('selected');
        const bid = b.dataset.id;
        const el = document.getElementById('check-'+bid);
        if(el) el.style.display = 'none';
    });
    btn.classList.add('selected');
    document.getElementById('check-'+id).style.display = '';
    selectedMetode = id;
}

async function prosesCheckout() {
    if (!selectedMetode) { alert('Pilih metode pembayaran dahulu'); return; }
    if (!currentTrxId) return;

    const btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Memproses...';

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
        if (data.success) {
            showSuccess(data);
        } else {
            alert(data.message || 'Gagal memproses checkout');
            btn.disabled = false;
            btn.innerHTML = '✅ Proses Checkout & Bayar';
        }
    } catch(e) {
        alert('Terjadi kesalahan');
        btn.disabled = false;
        btn.innerHTML = '✅ Proses Checkout & Bayar';
    }
}

function showSuccess(data) {
    document.querySelector('.px-4').innerHTML = `
        <div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4">
            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center text-5xl mb-5 animate-bounce">✅</div>
            <h2 class="text-2xl font-bold text-green-700 mb-2">Checkout Berhasil!</h2>
            <p class="text-slate-500 text-sm mb-6">Kendaraan telah selesai parkir</p>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 w-full max-w-sm mb-6 text-left">
                <div class="flex justify-between py-2 border-b border-slate-100 text-sm">
                    <span class="text-slate-500">No. Referensi</span>
                    <span class="font-bold text-slate-800">${data.nomor_referensi || '-'}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 text-sm">
                    <span class="text-slate-500">Durasi Parkir</span>
                    <span class="font-semibold text-orange-600">${data.durasi || '-'}</span>
                </div>
                <div class="flex justify-between py-2 text-sm">
                    <span class="text-slate-500">Total Bayar</span>
                    <span class="font-bold text-green-700 text-lg">Rp ${Number(data.total || 0).toLocaleString('id-ID')}</span>
                </div>
            </div>
            <a href="{{ route('petugas.dashboard') }}" class="w-full max-w-sm py-4 bg-blue-700 text-white rounded-xl font-bold text-base block text-center">
                Kembali ke Dashboard
            </a>
        </div>`;
}

function resetScan() {
    document.getElementById('resultSection').style.display = 'none';
    currentTrxId = null; selectedMetode = null;
    document.getElementById('detectedPlate').textContent = '-';
}

function showLoading(msg) {
    document.getElementById('loadingSection').style.display = '';
    document.getElementById('loadingText').textContent = msg || 'Memproses...';
}
function hideLoading() {
    document.getElementById('loadingSection').style.display = 'none';
}

// Init
startCamera();
</script>
@endpush
