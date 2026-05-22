@extends('layouts.admin')
@section('title', 'Lokasi Parkir')
@section('page-title', 'Lokasi Parkir')
@section('breadcrumb') <a href="{{ route('admin.dashboard') }}">Dashboard</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Lokasi Parkir @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-map-location-dot" style="color:var(--blue-light);margin-right:6px"></i>Data Lokasi Parkir</h4>
        <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Lokasi
        </a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Lokasi</th>
                        <th>Koordinat</th>
                        <th>Foto</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lokasi as $i => $l)
                    <tr>
                        <td>{{ $lokasi->firstItem() + $i }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $l->nama }}</div>
                        </td>
                        <td>
                            @if($l->koordinat)
                            <a href="https://maps.google.com/?q={{ $l->koordinat }}" target="_blank"
                               style="font-size:.82rem;color:var(--blue-light);text-decoration:none;">
                                <i class="fa-solid fa-map-pin"></i> {{ $l->koordinat }}
                            </a>
                            @else
                            <span style="color:var(--gray-400);font-size:.82rem;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($l->foto)
                            <img src="{{ asset('storage/'.$l->foto) }}" alt="" class="avatar-sm" style="border-radius:8px;width:48px;height:40px;object-fit:cover;">
                            @else
                            <div style="width:48px;height:40px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--gray-300);">
                                <i class="fa-solid fa-image"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.lokasi.edit', $l) }}" class="btn btn-sm btn-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.lokasi.destroy', $l) }}"
                                      onsubmit="return confirm('Hapus lokasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:var(--gray-400);">
                            <i class="fa-solid fa-map-location-dot" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                            Belum ada data lokasi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lokasi->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--gray-100);">
            {{ $lokasi->links('vendor.pagination.custom') }}
        </div>
        @endif
    </div>
</div>
@endsection
