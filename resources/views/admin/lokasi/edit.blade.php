@extends('layouts.admin')
@section('title', 'Edit Lokasi')
@section('page-title', 'Edit Lokasi Parkir')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i>
    <a href="{{ route('admin.lokasi.index') }}">Lokasi</a>
    <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Edit
@endsection

@section('content')
<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-pen" style="color:var(--blue-light);margin-right:6px"></i>Edit Lokasi: {{ $lokasi->nama }}</h4>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.lokasi.update', $lokasi) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Lokasi <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $lokasi->nama) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Koordinat GPS</label>
                    <input type="text" name="koordinat" class="form-control" value="{{ old('koordinat', $lokasi->koordinat) }}" placeholder="-3.0123,104.7456">
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Lokasi</label>
                    @if($lokasi->foto)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/'.$lokasi->foto) }}" alt="" style="height:80px;border-radius:8px;object-fit:cover;">
                        <p style="font-size:.75rem;color:var(--gray-400);margin-top:4px;">Foto saat ini. Upload baru untuk mengganti.</p>
                    </div>
                    @endif
                    <div id="dropzone" style="border:2px dashed var(--gray-200);border-radius:12px;padding:24px;text-align:center;cursor:pointer;" onclick="document.getElementById('foto').click()">
                        <div id="preview-wrap" style="display:none;margin-bottom:12px;"><img id="preview-img" src="" alt="" style="max-height:100px;border-radius:8px;margin:0 auto;"></div>
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:1.3rem;color:var(--gray-300);display:block;margin-bottom:6px;"></i>
                        <p style="font-size:.82rem;color:var(--gray-500);">Klik atau drag foto baru</p>
                    </div>
                    <input type="file" id="foto" name="foto" accept="image/*" style="display:none;" onchange="previewImage(this)">
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('admin.lokasi.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Perbarui Lokasi
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
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-wrap').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
