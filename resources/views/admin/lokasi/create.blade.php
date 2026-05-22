@extends('layouts.admin')
@section('title', 'Tambah Lokasi')
@section('page-title', 'Tambah Lokasi Parkir')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i>
    <a href="{{ route('admin.lokasi.index') }}">Lokasi</a>
    <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i>
    Tambah
@endsection

@section('content')
<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-plus" style="color:var(--blue-light);margin-right:6px"></i>Form Tambah Lokasi Parkir</h4>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.lokasi.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Lokasi <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="cth: Parkir Pasar Atas"
                           value="{{ old('nama') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Koordinat GPS</label>
                    <input type="text" name="koordinat" class="form-control"
                           placeholder="cth: -3.0123,104.7456" value="{{ old('koordinat') }}">
                    <small style="font-size:.76rem;color:var(--gray-400);margin-top:4px;display:block;">
                        Format: latitude,longitude — <a href="https://maps.google.com" target="_blank" style="color:var(--blue-light);">Cari di Google Maps</a>
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Lokasi</label>
                    <div id="dropzone" style="border:2px dashed var(--gray-200);border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:all .2s;background:var(--gray-50);"
                         onclick="document.getElementById('foto').click()">
                        <div id="preview-wrap" style="display:none;margin-bottom:12px;">
                            <img id="preview-img" src="" alt="" style="max-height:120px;border-radius:8px;margin:0 auto;">
                        </div>
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:1.5rem;color:var(--gray-300);margin-bottom:8px;display:block;"></i>
                        <p style="font-size:.85rem;color:var(--gray-500);">Klik untuk pilih foto atau drag & drop</p>
                        <small style="color:var(--gray-400);font-size:.75rem;">JPG, PNG, max 2MB</small>
                    </div>
                    <input type="file" id="foto" name="foto" accept="image/*" style="display:none;" onchange="previewImage(this)">
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('admin.lokasi.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-wrap').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

const dropzone = document.getElementById('dropzone');
dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.style.borderColor = 'var(--blue-light)'; dropzone.style.background = '#eff6ff'; });
dropzone.addEventListener('dragleave', () => { dropzone.style.borderColor = 'var(--gray-200)'; dropzone.style.background = 'var(--gray-50)'; });
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.style.borderColor = 'var(--gray-200)'; dropzone.style.background = 'var(--gray-50)';
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('foto').files = e.dataTransfer.files;
        previewImage(document.getElementById('foto'));
    }
});
</script>
@endpush
