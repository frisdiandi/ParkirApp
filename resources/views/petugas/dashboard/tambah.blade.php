@extends('layouts.petugas')
@section('title', 'Kendaraan Masuk')

@push('styles')
<style>
.page-header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 16px;
}
.page-header .back {
    width: 38px; height: 38px; border-radius: 12px;
    background: var(--white); border: 1px solid var(--gray-200);
    display: flex; align-items: center; justify-content: center;
    color: var(--gray-600); cursor: pointer;
}
.page-header h2 { font-size: 1.05rem; font-weight: 800; color: var(--charcoal); }
.page-header p  { font-size: .76rem; color: var(--gray-400); margin-top: 1px; }

#camera-section {
    background: #000; border-radius: 16px;
    overflow: hidden; position: relative; margin-bottom: 12px;
    aspect-ratio: 4/3; max-height: 280px;
}
#video { width: 100%; height: 100%; object-fit: cover; display: block; }
#canvas { display: none; }

.scan-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
}
.scan-frame {
    width: 78%; max-width: 280px; height: 38%; max-height: 110px;
    border: 2px solid rgba(255,255,255,.4);
    border-radius: 10px; position: relative;
    box-shadow: 0 0 0 9999px rgba(0,0,0,.4);
}
.scan-frame::before, .scan-frame::after,
.scan-frame > .c-tl, .scan-frame > .c-bl {
    content: ''; position: absolute; width: 22px; height: 22px;
    border-color: var(--accent); border-style: solid; border-width: 0;
}
.scan-frame::before { top: -3px; left: -3px; border-top-width: 3px; border-left-width: 3px; border-radius: 6px 0 0 0; }
.scan-frame::after  { bottom: -3px; right: -3px; border-bottom-width: 3px; border-right-width: 3px; border-radius: 0 0 6px 0; }
.scan-frame .c-tr { content:''; position: absolute; width:22px;height:22px;top:-3px;right:-3px;border-top:3px solid var(--accent);border-right:3px solid var(--accent);border-radius:0 6px 0 0; }
.scan-frame .c-bl { content:''; position: absolute; width:22px;height:22px;bottom:-3px;left:-3px;border-bottom:3px solid var(--accent);border-left:3px solid var(--accent);border-radius:0 0 6px 0; }
.scan-line {
    position: absolute; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    animation: scanAnim 1.8s ease-in-out infinite;
}
@keyframes scanAnim { 0% { top: 0; } 50% { top: calc(100% - 2px); } 100% { top: 0; } }
.scan-hint {
    position: absolute; bottom: 10px; left: 0; right: 0;
    text-align: center; color: rgba(255,255,255,.85);
    font-size: .75rem; font-weight: 600;
}

.cam-actions { display: grid; grid-template-columns: 1fr auto auto; gap: 8px; margin-bottom: 16px; }
.cam-btn {
    height: 46px; border-radius: 12px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    font-family: inherit; font-size: .85rem; font-weight: 700;
}
.cam-btn.primary { background: linear-gradient(135deg, var(--blue-light), var(--navy-mid)); color: #fff; }
.cam-btn.ghost { width: 46px; background: var(--gray-100); color: var(--gray-700); }

.plate-display {
    background: linear-gradient(135deg, var(--navy), var(--navy-mid));
    border-radius: 14px; padding: 14px 16px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px; gap: 10px;
}
.plate-display .label { font-size: .68rem; color: rgba(255,255,255,.6); font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
.plate-display .plate { font-size: 1.6rem; font-weight: 800; color: var(--white); font-family: 'Courier New', monospace; letter-spacing: 2px; margin-top: 2px; }
.plate-display .edit-btn { background: rgba(255,255,255,.18); border: none; border-radius: 8px; padding: 8px 12px; color: #fff; font-size: .76rem; cursor: pointer; font-family: inherit; font-weight: 700; }

.tarif-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
.tarif-option {
    padding: 14px 6px; border-radius: 14px; border: 2px solid var(--gray-200);
    background: var(--white); cursor: pointer; text-align: center;
    transition: all .15s; position: relative;
}
.tarif-option input { position: absolute; opacity: 0; width: 0; height: 0; }
.tarif-option .t-icon { font-size: 1.7rem; margin-bottom: 6px; line-height: 1; }
.tarif-option .t-name { font-size: .72rem; font-weight: 700; color: var(--charcoal); line-height: 1.2; }
.tarif-option .t-price { font-size: .68rem; color: var(--success); font-weight: 800; margin-top: 4px; }
.tarif-option.selected {
    border-color: var(--blue-light);
    background: var(--blue-soft);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(37,99,235,.18);
}
.tarif-option.selected .t-name { color: var(--blue-light); }

.processing-overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.7);
    display: none; align-items: center; justify-content: center; flex-direction: column;
    color: #fff; gap: 12px; z-index: 5;
}
.processing-overlay.show { display: flex; }
.processing-overlay .spinner { border-color: rgba(255,255,255,.3); border-top-color: #fff; }
.processing-overlay p { font-size: .85rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="page-header">
    <a href="{{ route('petugas.dashboard') }}" class="back"><i class="fa-solid fa-arrow-left"></i></a>
    <div>
        <h2>Kendaraan Masuk</h2>
        <p>Scan plat nomor atau ketik manual</p>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>
    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
</div>
@endif

<div id="camera-section">
    <video id="video" autoplay playsinline muted></video>
    <canvas id="canvas"></canvas>
    <div class="scan-overlay">
        <div class="scan-frame">
            <div class="c-tr"></div><div class="c-bl"></div>
            <div class="scan-line"></div>
        </div>
    </div>
    <div class="scan-hint">Arahkan kamera ke plat nomor lalu tekan tombol scan</div>
    <div class="processing-overlay" id="procOverlay">
        <div class="spinner"></div>
        <p id="procText">Memproses...</p>
    </div>
</div>

<div class="cam-actions">
    <button type="button" class="cam-btn primary" onclick="captureFrame()" id="btnScan">
        <i class="fa-solid fa-camera"></i> Scan Plat Nomor
    </button>
    <button type="button" class="cam-btn ghost" onclick="flipCamera()" title="Balik Kamera">
        <i class="fa-solid fa-rotate"></i>
    </button>
    <button type="button" class="cam-btn ghost" onclick="toggleTorch()" id="btnTorch" title="Senter">
        <i class="fa-solid fa-bolt"></i>
    </button>
</div>

<div class="plate-display" id="plateDisplay" style="{{ old('nomor_polisi') ? '' : 'display:none' }}">
    <div style="min-width:0;flex:1;">
        <div class="label">Plat Nomor Terdeteksi</div>
        <div class="plate" id="plateText">{{ old('nomor_polisi') }}</div>
    </div>
    <button class="edit-btn" onclick="clearPlate()" type="button">Ubah</button>
</div>

<form method="POST" action="{{ route('petugas.masuk') }}" id="formParkir">
    @csrf

    <div class="form-group">
        <label class="form-label">Nomor Polisi <span style="color:var(--danger)">*</span></label>
        <input type="text" name="nomor_polisi" id="nopolInput" class="form-control"
               placeholder="cth: BA 1234 AB"
               value="{{ old('nomor_polisi') }}"
               style="text-transform:uppercase;font-family:'Courier New',monospace;font-size:1.05rem;letter-spacing:1.5px;font-weight:700;"
               required maxlength="15">
    </div>

    <div class="form-group">
        <label class="form-label">Lokasi Parkir <span style="color:var(--danger)">*</span></label>
        <select name="id_lokasi" class="form-control" id="lokasiSelect" required>
            <option value="">— Pilih Lokasi —</option>
            @foreach($lokasi as $l)
            <option value="{{ $l->id }}" {{ old('id_lokasi') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Jenis Kendaraan / Tarif <span style="color:var(--danger)">*</span></label>
        <div class="tarif-grid">
            @foreach($tarif as $t)
            @php
                $n = strtolower($t->nama);
                if (str_contains($n,'2') || str_contains($n,'motor'))      $emoji = '🏍️';
                elseif (str_contains($n,'4') || str_contains($n,'mobil'))   $emoji = '🚗';
                elseif (str_contains($n,'6') || str_contains($n,'truk') || str_contains($n,'bus')) $emoji = '🚛';
                else $emoji = '🚙';
            @endphp
            <label class="tarif-option {{ old('id_tarif') == $t->id ? 'selected' : '' }}" onclick="selectTarif(this)">
                <input type="radio" name="id_tarif" value="{{ $t->id }}" {{ old('id_tarif') == $t->id ? 'checked' : '' }} required>
                <div class="t-icon">{{ $emoji }}</div>
                <div class="t-name">{{ $t->nama }}</div>
                <div class="t-price">Rp {{ number_format($t->tarif,0,',','.') }}</div>
            </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;padding:14px;font-size:.95rem;" id="btnSimpan">
        <i class="fa-solid fa-circle-check"></i> Catat Kendaraan Masuk
    </button>
    <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary btn-full" style="margin-top:8px;">Batal</a>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.1.1/tesseract.min.js"></script>
<script>
let stream = null;
let useFront = false;
let torchOn = false;
const video  = document.getElementById('video');
const canvas = document.getElementById('canvas');
const proc   = document.getElementById('procOverlay');
const procText = document.getElementById('procText');

function showProc(t) { procText.textContent = t || 'Memproses...'; proc.classList.add('show'); }
function hideProc() { proc.classList.remove('show'); }

async function startCamera() {
    try {
        if (stream) stream.getTracks().forEach(t => t.stop());
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: useFront ? 'user' : { ideal: 'environment' },
                width: { ideal: 1280 }, height: { ideal: 720 }
            }
        });
        video.srcObject = stream;
    } catch (e) {
        console.warn('Camera unavailable:', e);
        document.getElementById('camera-section').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:10px;color:rgba(255,255,255,.55);padding:20px;text-align:center;">
                <i class="fa-solid fa-camera-slash" style="font-size:2.2rem;"></i>
                <p style="font-size:.82rem;">Kamera tidak tersedia.<br>Silakan isi nomor polisi manual.</p>
            </div>`;
    }
}

function flipCamera() { useFront = !useFront; torchOn = false; startCamera(); }

async function toggleTorch() {
    if (!stream) return;
    const track = stream.getVideoTracks()[0];
    try {
        torchOn = !torchOn;
        await track.applyConstraints({ advanced: [{ torch: torchOn }] });
        document.getElementById('btnTorch').style.background = torchOn ? '#fef3c7' : '';
        document.getElementById('btnTorch').style.color = torchOn ? '#d97706' : '';
    } catch (e) { alert('Senter tidak didukung perangkat ini'); }
}

// Pre-process gambar untuk OCR: greyscale + threshold
function preprocessForOcr(srcCanvas) {
    const ctx = srcCanvas.getContext('2d');
    const img = ctx.getImageData(0, 0, srcCanvas.width, srcCanvas.height);
    const d = img.data;
    // greyscale + contrast threshold
    for (let i = 0; i < d.length; i += 4) {
        const g = (d[i] * 0.299 + d[i+1] * 0.587 + d[i+2] * 0.114);
        const v = g > 130 ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
    }
    ctx.putImageData(img, 0, 0);
    return srcCanvas;
}

// Crop ke area scan frame (78% lebar, 38% tinggi tengah)
function cropToScanArea(srcCanvas) {
    const w = srcCanvas.width, h = srcCanvas.height;
    const cw = Math.floor(w * 0.78);
    const ch = Math.floor(h * 0.38);
    const cx = Math.floor((w - cw) / 2);
    const cy = Math.floor((h - ch) / 2);

    const out = document.createElement('canvas');
    out.width = cw; out.height = ch;
    out.getContext('2d').drawImage(srcCanvas, cx, cy, cw, ch, 0, 0, cw, ch);
    return out;
}

async function captureFrame() {
    if (!stream) { alert('Kamera belum siap'); return; }

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    showProc('Membaca plat nomor...');

    try {
        const cropped = cropToScanArea(canvas);
        const prepped = preprocessForOcr(cropped);

        const { data: { text } } = await Tesseract.recognize(prepped, 'eng', {
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
            tessedit_pageseg_mode:   '7'  // single line
        });

        // Bersihkan dan cari pola plat Indonesia: 1-2 huruf, spasi, 1-4 angka, spasi, 1-3 huruf
        const cleaned = text.replace(/[^A-Z0-9 ]/gi, ' ').replace(/\s+/g, ' ').trim().toUpperCase();
        const match   = cleaned.match(/[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{0,3}/);

        let plate = '';
        if (match) {
            plate = match[0].replace(/([A-Z]+)(\d+)([A-Z]*)/, '$1 $2 $3').replace(/\s+/g, ' ').trim();
        } else if (cleaned.length >= 4) {
            // fallback: ambil sebanyak mungkin huruf/angka
            plate = cleaned.substring(0, 12).trim();
        }

        hideProc();

        if (plate) {
            setPlate(plate);
        } else {
            alert('Plat nomor tidak terbaca. Coba lagi atau ketik manual.');
        }
    } catch (e) {
        console.error(e);
        hideProc();
        alert('Gagal memproses. Silakan ketik manual.');
    }
}

function setPlate(val) {
    document.getElementById('nopolInput').value = val;
    document.getElementById('plateText').textContent = val;
    document.getElementById('plateDisplay').style.display = 'flex';
    document.getElementById('nopolInput').focus();
}

function clearPlate() {
    document.getElementById('nopolInput').value = '';
    document.getElementById('plateDisplay').style.display = 'none';
    document.getElementById('nopolInput').focus();
}

function selectTarif(el) {
    document.querySelectorAll('.tarif-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}

document.addEventListener('DOMContentLoaded', () => {
    // Auto-set lokasi dari localStorage
    const lokasiId = localStorage.getItem('lokasiAktif');
    if (lokasiId) {
        const sel = document.getElementById('lokasiSelect');
        if (sel) {
            for (let opt of sel.options) {
                if (opt.value == lokasiId) { opt.selected = true; break; }
            }
        }
    }

    document.getElementById('nopolInput').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        if (this.value) {
            document.getElementById('plateText').textContent = this.value;
            document.getElementById('plateDisplay').style.display = 'flex';
        }
    });

    document.getElementById('formParkir').addEventListener('submit', function(e) {
        const np = document.getElementById('nopolInput').value.trim();
        const tarif = document.querySelector('input[name="id_tarif"]:checked');
        const lok = document.getElementById('lokasiSelect').value;
        if (!np || !tarif || !lok) {
            e.preventDefault();
            alert('Lengkapi semua data: nomor polisi, lokasi, dan tarif.');
            return false;
        }
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    });

    startCamera();
});

window.addEventListener('pagehide', () => {
    if (stream) stream.getTracks().forEach(t => t.stop());
});
</script>
@endpush
