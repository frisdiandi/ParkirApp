@extends('layouts.petugas')
@section('title', 'Tambah Parkir')

@push('styles')
<style>
.page-header { margin-bottom: 16px; }
.page-header h2 { font-size: 1.1rem; font-weight: 800; color: var(--charcoal); }
.page-header p  { font-size: .8rem; color: var(--gray-400); margin-top: 2px; }

#camera-section {
    background: var(--charcoal); border-radius: 16px;
    overflow: hidden; position: relative; margin-bottom: 16px;
    aspect-ratio: 4/3;
}
#video { width: 100%; height: 100%; object-fit: cover; display: block; }
#canvas { display: none; }

.scan-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center; flex-direction: column;
    pointer-events: none;
}
.scan-frame {
    width: 220px; height: 80px; border: 2px solid rgba(255,255,255,.6);
    border-radius: 8px; position: relative;
}
.scan-frame::before, .scan-frame::after {
    content: ''; position: absolute; width: 20px; height: 20px;
    border-color: var(--accent); border-style: solid;
}
.scan-frame::before { top: -2px; left: -2px; border-width: 2px 0 0 2px; border-radius: 4px 0 0 0; }
.scan-frame::after  { bottom: -2px; right: -2px; border-width: 0 2px 2px 0; border-radius: 0 0 4px 0; }
.scan-line {
    position: absolute; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
    animation: scanAnim 1.5s ease-in-out infinite;
}
@keyframes scanAnim {
    0% { top: 0; } 100% { top: 100%; }
}
.scan-hint { margin-top: 12px; font-size: .72rem; color: rgba(255,255,255,.7); font-weight: 500; }

.camera-controls {
    position: absolute; bottom: 10px; right: 10px;
    display: flex; gap: 6px;
}
.cam-btn {
    width: 34px; height: 34px; border-radius: 8px;
    background: rgba(255,255,255,.2); border: none; cursor: pointer;
    color: white; font-size: .8rem; backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
}

.plate-display {
    background: var(--navy); border-radius: 14px; padding: 14px 16px;
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px;
}
.plate-display .label { font-size: .72rem; color: rgba(255,255,255,.5); font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }
.plate-display .plate { font-size: 1.5rem; font-weight: 800; color: var(--white); font-family: monospace; letter-spacing: 2px; margin-top: 2px; }
.plate-display .edit-btn { background: rgba(255,255,255,.15); border: none; border-radius: 8px; padding: 8px 12px; color: white; font-size: .78rem; cursor: pointer; font-family: inherit; }

.tarif-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px; }
.tarif-option {
    padding: 14px 10px; border-radius: 14px; border: 2px solid var(--gray-200);
    background: var(--white); cursor: pointer; text-align: center;
    transition: all .2s; position: relative;
}
.tarif-option input { position: absolute; opacity: 0; width: 0; height: 0; }
.tarif-option .t-icon { font-size: 1.5rem; margin-bottom: 6px; }
.tarif-option .t-name { font-size: .78rem; font-weight: 600; color: var(--charcoal); }
.tarif-option .t-price { font-size: .72rem; color: var(--success); font-weight: 700; margin-top: 2px; }
.tarif-option.selected {
    border-color: var(--blue-light);
    background: #eff6ff;
}
.tarif-option.selected .t-name { color: var(--blue-light); }
</style>
@endpush

@section('content')
<div class="page-header">
    <h2>Kendaraan Masuk</h2>
    <p>Scan nomor polisi atau isi manual</p>
</div>

@if($errors->any())
<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
@endif

<!-- Camera -->
<div id="camera-section">
    <video id="video" autoplay playsinline muted></video>
    <canvas id="canvas"></canvas>
    <div class="scan-overlay">
        <div class="scan-frame">
            <div class="scan-line"></div>
        </div>
        <p class="scan-hint">Arahkan ke plat nomor kendaraan</p>
    </div>
    <div class="camera-controls">
        <button class="cam-btn" onclick="captureFrame()" title="Tangkap">
            <i class="fa-solid fa-camera"></i>
        </button>
        <button class="cam-btn" onclick="flipCamera()" title="Balik Kamera">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>
</div>

<!-- OCR Result -->
<div class="plate-display" id="plateDisplay" style="{{ old('nomor_polisi') ? '' : 'display:none' }}">
    <div>
        <div class="label">Nomor Polisi Terdeteksi</div>
        <div class="plate" id="plateText">{{ old('nomor_polisi') }}</div>
    </div>
    <button class="edit-btn" onclick="clearPlate()">Ubah</button>
</div>

<form method="POST" action="{{ route('petugas.masuk') }}" id="formParkir">
    @csrf

    <div class="form-group">
        <label class="form-label">Nomor Polisi <span style="color:red">*</span></label>
        <input type="text" name="nomor_polisi" id="nopolInput" class="form-control"
               placeholder="cth: BG 1234 AB"
               value="{{ old('nomor_polisi') }}"
               style="text-transform:uppercase;font-family:monospace;font-size:1rem;letter-spacing:1px;font-weight:700;"
               required>
    </div>

    <div class="form-group">
        <label class="form-label">Lokasi Parkir <span style="color:red">*</span></label>
        <select name="id_lokasi" class="form-control" id="lokasiSelect" required>
            <option value="">— Pilih Lokasi —</option>
            @foreach($lokasi as $l)
            <option value="{{ $l->id }}" {{ old('id_lokasi') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">Jenis Kendaraan / Tarif <span style="color:red">*</span></label>
        <div class="tarif-grid">
            @foreach($tarif as $t)
            <label class="tarif-option {{ old('id_tarif') == $t->id ? 'selected' : '' }}" onclick="selectTarif(this)">
                <input type="radio" name="id_tarif" value="{{ $t->id }}" {{ old('id_tarif') == $t->id ? 'checked' : '' }}>
                <div class="t-icon">
                    @php
                        $n = strtolower($t->nama);
                        if(str_contains($n,'2') || str_contains($n,'motor')) echo '🏍️';
                        elseif(str_contains($n,'4') || str_contains($n,'mobil')) echo '🚗';
                        else echo '🚛';
                    @endphp
                </div>
                <div class="t-name">{{ $t->nama }}</div>
                <div class="t-price">{{ $t->tarif_format }}</div>
            </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;padding:14px;">
        <i class="fa-solid fa-car-on"></i> Catat Kendaraan Masuk
    </button>
    <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary btn-full" style="margin-top:8px;">Batal</a>
</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.0.2/tesseract.min.js"></script>
<script>
let stream = null;
let useFront = false;
const video  = document.getElementById('video');
const canvas = document.getElementById('canvas');

async function startCamera() {
    try {
        if (stream) stream.getTracks().forEach(t => t.stop());
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: useFront ? 'user' : 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        video.srcObject = stream;
    } catch (e) {
        console.log('Camera not available:', e);
        document.getElementById('camera-section').innerHTML = `
            <div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:8px;color:rgba(255,255,255,.5);padding:20px;text-align:center;">
                <i class="fa-solid fa-camera-slash" style="font-size:2rem;"></i>
                <p style="font-size:.8rem;">Kamera tidak tersedia. Isi nomor polisi manual.</p>
            </div>`;
    }
}

function flipCamera() { useFront = !useFront; startCamera(); }

async function captureFrame() {
    if (!stream) return;
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const imgData = canvas.toDataURL('image/jpeg', 0.9);

    // Simple OCR with Tesseract
    try {
        const btn = document.querySelector('.cam-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        const { data: { text } } = await Tesseract.recognize(imgData, 'eng', {
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ',
        });

        // Extract plate-like pattern
        const cleaned = text.replace(/\n/g,' ').replace(/\s+/g,' ').trim();
        const match   = cleaned.match(/[A-Z]{1,2}\s*\d{1,4}\s*[A-Z]{0,3}/g);
        const plate   = match ? match[0].replace(/\s+/g,' ').trim() : cleaned.substring(0, 12).trim();

        if (plate) {
            setPlate(plate.toUpperCase());
        }

        btn.innerHTML = '<i class="fa-solid fa-camera"></i>';
    } catch (e) {
        console.error(e);
    }
}

function setPlate(val) {
    document.getElementById('nopolInput').value = val;
    document.getElementById('plateText').textContent = val;
    document.getElementById('plateDisplay').style.display = 'flex';
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

// Auto-set lokasi from localStorage
document.addEventListener('DOMContentLoaded', () => {
    const lokasiId = localStorage.getItem('lokasiAktif');
    if (lokasiId) {
        const sel = document.getElementById('lokasiSelect');
        if (sel) {
            for (let opt of sel.options) {
                if (opt.value == lokasiId) { opt.selected = true; break; }
            }
        }
    }

    // Auto-uppercase input
    document.getElementById('nopolInput').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    startCamera();
});
</script>
@endpush
