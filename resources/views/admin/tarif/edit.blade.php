@extends('layouts.admin')
@section('title', 'Edit Tarif')
@section('page-title', 'Edit Tarif Parkir')
@section('breadcrumb') <a href="{{ route('admin.tarif.index') }}">Tarif</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Edit @endsection

@section('content')
<div style="max-width:580px;">
    <div class="card">
        <div class="card-header"><h4>Edit Tarif: {{ $tarif->nama }}</h4></div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>
            @endif

            <form method="POST" action="{{ route('admin.tarif.update', $tarif) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">Jenis Kendaraan <span style="color:red">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $tarif->nama) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tarif (Rp) <span style="color:red">*</span></label>
                    <div style="position:relative;">
                        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--gray-400);font-size:.85rem;">Rp</span>
                        <input type="number" name="tarif" class="form-control" style="padding-left:36px;" value="{{ old('tarif', $tarif->tarif) }}" required min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Foto Kendaraan</label>
                    @if($tarif->foto)
                    <div style="margin-bottom:8px;"><img src="{{ asset('storage/'.$tarif->foto) }}" style="height:60px;border-radius:6px;"></div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="form-control">
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <a href="{{ route('admin.tarif.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
