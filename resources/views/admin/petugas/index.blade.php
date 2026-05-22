@extends('layouts.admin')
@section('title', 'Data Petugas')
@section('page-title', 'Data Petugas')
@section('breadcrumb') <a href="{{ route('admin.dashboard') }}">Dashboard</a> <i class="fa-solid fa-chevron-right" style="font-size:.6rem"></i> Data Petugas @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fa-solid fa-users" style="color:var(--blue-light);margin-right:6px"></i>Daftar Petugas Parkir</h4>
        <a href="{{ route('admin.petugas.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Tambah Petugas</a>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Petugas</th>
                        <th>Username</th>
                        <th>Contact</th>
                        <th>Nomor Rekening</th>
                        <th>Lokasi Tugas</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petugas as $i => $p)
                    @php $u = $p->user; @endphp
                    <tr>
                        <td>{{ $petugas->firstItem() + $i }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" alt="" class="avatar-sm" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
                                @else
                                <div style="width:40px;height:40px;border-radius:10px;background:var(--blue-light);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.9rem;flex-shrink:0;">
                                    {{ substr($u->nama ?? 'P', 0, 1) }}
                                </div>
                                @endif
                                <div>
                                    <div style="font-weight:600;font-size:.88rem;">{{ $u->nama }}</div>
                                    <div style="font-size:.72rem;color:var(--gray-400);">ID: {{ $p->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-family:monospace;font-size:.85rem;">{{ $u->username }}</td>
                        <td style="font-size:.85rem;">{{ $u->contact ?? '—' }}</td>
                        <td style="font-size:.85rem;">
                            @if($p->nomor_rekening)
                            <span style="font-family:monospace;">{{ $p->nomor_rekening }}</span>
                            @else
                            <span style="color:var(--gray-300);">—</span>
                            @endif
                        </td>
                        <td>
                            @php $ids = is_array($p->id_lokasi) ? $p->id_lokasi : []; @endphp
                            @if(count($ids))
                            @foreach(\App\Models\Lokasi::whereIn('id',$ids)->get() as $lok)
                            <span class="badge-pill badge-info" style="margin:2px 2px 2px 0;display:inline-block;">{{ $lok->nama }}</span>
                            @endforeach
                            @else
                            <span style="color:var(--gray-300);font-size:.82rem;">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-pill {{ $u->status ? 'badge-success' : 'badge-danger' }}">
                                <i class="fa-solid fa-circle" style="font-size:.5rem;"></i>
                                {{ $u->status_label }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.petugas.edit', $p) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                                <form method="POST" action="{{ route('admin.petugas.destroy', $p) }}" onsubmit="return confirm('Hapus petugas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-400);">
                        <i class="fa-solid fa-users" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data petugas
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($petugas->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--gray-100);">{{ $petugas->links() }}</div>
        @endif
    </div>
</div>
@endsection