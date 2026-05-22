@extends('layouts.admin')
@section('title', 'Metode Pembayaran')
@section('page-title', 'Metode Pembayaran')
@section('breadcrumb') <a href="{{ route('admin.dashboard') }}">Dashboard</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Metode Pembayaran @endsection

@push('styles')
<style>
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:white;border-radius:16px;padding:28px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-box h4 { font-size:1rem;font-weight:700;margin-bottom:20px;color:var(--charcoal); }
</style>
@endpush

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">
    <!-- List -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-credit-card" style="color:var(--blue-light);margin-right:6px"></i>Daftar Metode Pembayaran</h4>
            <button class="btn btn-primary" onclick="openModal()"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        <div class="card-body" style="padding:0;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Metode</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metode as $i => $m)
                    <tr>
                        <td>{{ $metode->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--blue-light);">
                                    <i class="fa-solid fa-{{ strtolower($m->nama) == 'cash' ? 'money-bill' : 'qrcode' }}"></i>
                                </div>
                                {{ $m->nama }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <button class="btn btn-sm btn-warning" onclick="openEdit({{ $m->id }}, '{{ $m->nama }}')">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.metode.destroy', $m) }}" onsubmit="return confirm('Hapus metode ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--gray-400);">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info card -->
    <div class="card">
        <div class="card-header"><h4>Informasi</h4></div>
        <div class="card-body">
            <p style="font-size:.86rem;color:var(--gray-600);line-height:1.7;">
                Metode pembayaran digunakan saat petugas melakukan <strong>checkout</strong> kendaraan.
                Petugas dapat memilih metode Cash atau QRIS sesuai yang dibayar oleh pengemudi.
            </p>
            <div style="margin-top:16px;display:flex;gap:12px;">
                <div style="flex:1;background:var(--gray-50);border-radius:12px;padding:14px;text-align:center;">
                    <i class="fa-solid fa-money-bill-wave" style="color:var(--success);font-size:1.3rem;"></i>
                    <p style="font-size:.8rem;margin-top:6px;font-weight:600;">Cash</p>
                    <p style="font-size:.72rem;color:var(--gray-400);">Uang tunai langsung</p>
                </div>
                <div style="flex:1;background:var(--gray-50);border-radius:12px;padding:14px;text-align:center;">
                    <i class="fa-solid fa-qrcode" style="color:var(--blue-light);font-size:1.3rem;"></i>
                    <p style="font-size:.8rem;margin-top:6px;font-weight:600;">QRIS</p>
                    <p style="font-size:.72rem;color:var(--gray-400);">Scan & bayar digital</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal-overlay" id="modalTambah">
    <div class="modal-box">
        <h4><i class="fa-solid fa-plus" style="color:var(--blue-light);margin-right:6px"></i> Tambah Metode Pembayaran</h4>
        @if($errors->has('nama'))
        <div class="alert alert-danger" style="margin-bottom:16px;"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('nama') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.metode.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Metode <span style="color:red">*</span></label>
                <input type="text" name="nama" class="form-control" placeholder="cth: Cash, QRIS, Transfer" value="{{ old('nama') }}" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal-overlay" id="modalEdit">
    <div class="modal-box">
        <h4><i class="fa-solid fa-pen" style="color:var(--warning);margin-right:6px"></i> Edit Metode Pembayaran</h4>
        <form method="POST" id="formEdit">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Metode <span style="color:red">*</span></label>
                <input type="text" name="nama" id="editNama" class="form-control" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal() { document.getElementById('modalTambah').classList.add('open'); }
function closeModal() { document.getElementById('modalTambah').classList.remove('open'); }
function openEdit(id, nama) {
    document.getElementById('editNama').value = nama;
    document.getElementById('formEdit').action = `/admin/metode/${id}`;
    document.getElementById('modalEdit').classList.add('open');
}
function closeEditModal() { document.getElementById('modalEdit').classList.remove('open'); }

@if($errors->any())
openModal();
@endif
</script>
@endpush
