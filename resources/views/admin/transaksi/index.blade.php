@extends('layouts.admin')
@section('title', 'Data Transaksi')
@section('page-title', 'Data Transaksi')
@section('breadcrumb') <a href="{{ route('admin.dashboard') }}">Dashboard</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Data Transaksi @endsection

@push('styles')
<style>
.filter-bar { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:12px; margin-bottom:20px; }
</style>
@endpush

@section('content')
<!-- Filter -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.transaksi.index') }}">
            <div class="filter-bar">
                <div>
                    <label class="form-label" style="font-size:.78rem;">Tanggal Dari</label>
                    <input type="date" name="tgl_dari" class="form-control" value="{{ request('tgl_dari') }}">
                </div>
                <div>
                    <label class="form-label" style="font-size:.78rem;">Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" class="form-control" value="{{ request('tgl_sampai') }}">
                </div>
                <div>
                    <label class="form-label" style="font-size:.78rem;">Lokasi</label>
                    <select name="id_lokasi" class="form-control">
                        <option value="">— Semua Lokasi —</option>
                        @foreach($lokasi as $l)
                        <option value="{{ $l->id }}" {{ request('id_lokasi') == $l->id ? 'selected' : '' }}>{{ $l->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:.78rem;">Status</label>
                    <select name="status" class="form-control">
                        <option value="">— Semua Status —</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:.78rem;">Nomor Polisi</label>
                    <input type="text" name="nomor_polisi" class="form-control" placeholder="cth: BG1234AB" value="{{ request('nomor_polisi') }}">
                </div>
                <div style="display:flex;align-items:flex-end;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fa-solid fa-search"></i> Filter</button>
                    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-secondary"><i class="fa-solid fa-refresh"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary row -->
<div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="background:var(--white);border-radius:12px;padding:14px 20px;border:1px solid var(--gray-200);flex:1;min-width:180px;">
        <div style="font-size:.76rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Total Transaksi</div>
        <div style="font-size:1.4rem;font-weight:800;margin-top:4px;">{{ $transaksi->total() }}</div>
    </div>
    <div style="background:var(--white);border-radius:12px;padding:14px 20px;border:1px solid var(--gray-200);flex:1;min-width:180px;">
        <div style="font-size:.76rem;color:var(--gray-400);font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Total Pendapatan</div>
        <div style="font-size:1.4rem;font-weight:800;margin-top:4px;color:var(--success);">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-receipt" style="color:var(--blue-light);margin-right:6px"></i>Data Transaksi Parkir</h4>
        <span style="font-size:.82rem;color:var(--gray-400);">{{ $transaksi->total() }} data</span>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Referensi</th>
                        <th>Tanggal</th>
                        <th>Nomor Polisi</th>
                        <th>Lokasi</th>
                        <th>Tarif</th>
                        <th>Petugas</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr>
                        <td><code style="font-size:.78rem;background:var(--gray-100);padding:3px 7px;border-radius:4px;">{{ $t->nomor_referensi }}</code></td>
                        <td style="font-size:.83rem;">{{ $t->tgl->format('d/m/Y') }}</td>
                        <td>
                            <span style="font-weight:700;font-family:monospace;font-size:.9rem;background:var(--navy);color:white;padding:3px 8px;border-radius:6px;">{{ $t->nomor_polisi }}</span>
                        </td>
                        <td style="font-size:.83rem;">{{ $t->lokasi?->nama }}</td>
                        <td>
                            <div style="font-size:.82rem;font-weight:600;">{{ $t->tarif?->nama }}</div>
                            <div style="font-size:.75rem;color:var(--success);">{{ $t->tarif?->tarif_format }}</div>
                        </td>
                        <td style="font-size:.83rem;">{{ $t->petugas?->user?->nama }}</td>
                        <td style="font-size:.83rem;">{{ $t->jam_masuk }}</td>
                        <td style="font-size:.83rem;">{{ $t->jam_keluar ?? '—' }}</td>
                        <td>
                            @if($t->metode)
                            <span class="badge-pill badge-info">{{ $t->metode->nama }}</span>
                            @else
                            <span style="color:var(--gray-300);font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-pill {{ $t->status ? 'badge-success' : 'badge-warning' }}">
                                {{ $t->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.transaksi.show', $t) }}" class="btn btn-sm btn-secondary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" style="text-align:center;padding:40px;color:var(--gray-400);">
                        <i class="fa-solid fa-receipt" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Tidak ada data transaksi
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transaksi->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--gray-100);">{{ $transaksi->links() }}</div>
        @endif
    </div>
</div>
@endsection
