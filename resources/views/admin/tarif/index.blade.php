@extends('layouts.admin')
@section('title', 'Tarif Parkir')
@section('page-title', 'Tarif Parkir')
@section('breadcrumb') <a href="{{ route('admin.dashboard') }}">Dashboard</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Tarif Parkir @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-tags" style="color:var(--blue-light);margin-right:6px"></i>Data Tarif Parkir</h4>
        <a href="{{ route('admin.tarif.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tarif</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Jenis Kendaraan</th>
                        <th>Tarif</th>
                        <th>Foto</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarif as $i => $t)
                    <tr>
                        <td>{{ $tarif->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @php
                                    $icon = str_contains(strtolower($t->nama), '2') || str_contains(strtolower($t->nama), 'motor') ? 'fa-motorcycle' : (str_contains(strtolower($t->nama), '4') || str_contains(strtolower($t->nama), 'mobil') ? 'fa-car' : 'fa-truck');
                                @endphp
                                <div style="width:34px;height:34px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--blue-light);">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <span style="font-weight:600;">{{ $t->nama }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-pill badge-success" style="font-size:.85rem;">
                                {{ $t->tarif_format }}
                            </span>
                        </td>
                        <td>
                            @if($t->foto)
                            <img src="{{ asset('storage/'.$t->foto) }}" alt="" style="width:50px;height:38px;object-fit:cover;border-radius:6px;">
                            @else
                            <span style="color:var(--gray-300);">—</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.tarif.edit', $t) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                                <form method="POST" action="{{ route('admin.tarif.destroy', $t) }}" onsubmit="return confirm('Hapus tarif ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--gray-400);">Belum ada data tarif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
