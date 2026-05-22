@extends('layouts.admin')
@section('title', 'Edit Petugas')
@section('page-title', 'Edit Petugas')
@section('breadcrumb') <a href="{{ route('admin.petugas.index') }}">Petugas</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Edit @endsection

@section('content')
<div style="max-width:720px;">
    <div class="card">
        <div class="card-header"><h4><i class="fa-solid fa-pen" style="color:var(--blue-light);margin-right:6px"></i>Edit Petugas: {{ $petugas->user->nama }}</h4></div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
            @endif

            <form method="POST" action="{{ route('admin.petugas.update', $petugas) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $petugas->user->nama) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username <span style="color:red">*</span></label>
                        <input type="text" name="username" class="form-control" value="{{ old('username', $petugas->user->username) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru <small style="color:var(--gray-400)">(kosongkan jika tidak diganti)</small></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor HP / Contact</label>
                        <input type="text" name="contact" class="form-control" value="{{ old('contact', $petugas->user->contact) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" name="nomor_rekening" class="form-control" value="{{ old('nomor_rekening', $petugas->nomor_rekening) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Akun</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $petugas->user->status) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status', $petugas->user->status) == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Lokasi Tugas</label>
                    @php $lokasiAktif = $petugas->id_lokasi ?? []; @endphp
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;padding:14px;background:var(--gray-50);border-radius:12px;border:1.5px solid var(--gray-200);">
                        @foreach($lokasi as $l)
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.86rem;font-weight:500;padding:8px;border-radius:8px;">
                            <input type="checkbox" name="id_lokasi[]" value="{{ $l->id }}"
                                   style="accent-color:var(--blue-light);width:16px;height:16px;"
                                   {{ in_array($l->id, old('id_lokasi', $lokasiAktif)) ? 'checked' : '' }}>
                            {{ $l->nama }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Petugas</label>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <div id="foto-preview" style="width:80px;height:80px;border-radius:12px;overflow:hidden;flex-shrink:0;background:var(--gray-100);display:flex;align-items:center;justify-content:center;">
                            @if($petugas->foto)
                            <img src="{{ asset('storage/'.$petugas->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                            <i class="fa-solid fa-user" style="font-size:1.5rem;color:var(--gray-300);"></i>
                            @endif
                        </div>
                        <div style="flex:1;">
                            <input type="file" name="foto" id="fotoInput" accept="image/*" class="form-control" onchange="previewFoto(this)">
                            <small style="font-size:.75rem;color:var(--gray-400);display:block;margin-top:4px;">Upload baru untuk mengganti foto</small>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid var(--gray-100);">
                    <a href="{{ route('admin.petugas.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
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
            document.getElementById('foto-preview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
