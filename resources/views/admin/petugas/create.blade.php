@extends('layouts.admin')
@section('title', 'Tambah Petugas')
@section('page-title', 'Tambah Petugas')
@section('breadcrumb') <a href="{{ route('admin.petugas.index') }}">Petugas</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Tambah @endsection

@section('content')
<div style="max-width:720px;">
    <div class="card">
        <div class="card-header"><h4><i class="fa-solid fa-user-plus" style="color:var(--blue-light);margin-right:6px"></i>Form Tambah Petugas</h4></div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
            @endif

            <form method="POST" action="{{ route('admin.petugas.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Nama petugas" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username <span style="color:red">*</span></label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Username login" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span style="color:red">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password <span style="color:red">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP / Contact</label>
                        <input type="text" name="contact" class="form-control" value="{{ old('contact') }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="nomor_rekening" class="form-control" value="{{ old('nomor_rekening') }}" placeholder="cth: 1234567890">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Akun <span style="color:red">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="1" {{ old('status','1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi Tugas (bisa pilih lebih dari 1)</label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;padding:14px;background:var(--gray-50);border-radius:12px;border:1.5px solid var(--gray-200);">
                        @foreach($lokasi as $l)
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.86rem;font-weight:500;padding:8px;border-radius:8px;transition:background .15s;" onmouseover="this.style.background='var(--gray-100)'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" name="id_lokasi[]" value="{{ $l->id }}"
                                   style="accent-color:var(--blue-light);width:16px;height:16px;"
                                   {{ in_array($l->id, old('id_lokasi', [])) ? 'checked' : '' }}>
                            {{ $l->nama }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Petugas</label>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div id="foto-preview" style="width:80px;height:80px;border-radius:12px;background:var(--gray-100);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                            <i class="fa-solid fa-user" style="font-size:1.5rem;color:var(--gray-300);"></i>
                        </div>
                        <div style="flex:1;">
                            <input type="file" name="foto" id="fotoInput" accept="image/*" class="form-control" onchange="previewFoto(this)">
                            <small style="font-size:.75rem;color:var(--gray-400);display:block;margin-top:4px;">JPG, PNG, maks 2MB. Foto profil petugas.</small>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--gray-100);">
                    <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Petugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('foto-preview');
            prev.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
