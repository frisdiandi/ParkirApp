@extends('layouts.petugas')

@section('title', 'Riwayat Transaksi')

@push('styles')
<style>
.filter-chip {
    display: inline-flex; align-items: center;
    padding: 6px 14px; border-radius: 20px;
    font-size: 0.78rem; font-weight: 600;
    border: 1.5px solid #e2e8f0;
    background: #fff; color: #64748b;
    cursor: pointer; transition: all 0.15s;
    white-space: nowrap;
}
.filter-chip.active { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }

.trx-item {
    background: #fff;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    padding: 14px 16px;
    margin-bottom: 10px;
    display: flex; align-items: center; gap: 12px;
    cursor: pointer;
    transition: box-shadow 0.15s, transform 0.1s;
    text-decoration: none;
    color: inherit;
}
.trx-item:active { transform: scale(0.98); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.trx-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.status-lunas { background: #dcfce7; }
.status-belum { background: #fff7ed; }
.badge {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 10px;
    font-size: 0.7rem; font-weight: 700;
}
.badge-lunas { background: #dcfce7; color: #16a34a; }
.badge-belum { background: #fff7ed; color: #ea580c; }

.summary-bar {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 8px; margin-bottom: 16px;
}
.summary-item {
    background: #fff; border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    padding: 10px 12px; text-align: center;
}
.summary-val { font-size: 1.1rem; font-weight: 800; color: #1e3a5f; }
.summary-lbl { font-size: 0.68rem; color: #94a3b8; margin-top: 2px; }

.date-badge {
    font-size: 0.72rem; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 8px 0 4px;
}

.plate-chip {
    font-family: 'Courier New', monospace;
    font-weight: 700; font-size: 0.9rem;
    color: #1e3a5f;
}
</style>
@endpush

@section('content')
<div class="px-4 pt-2 pb-28">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-bold text-slate-800">Riwayat Parkir</h1>
            <p class="text-xs text-slate-500">{{ auth()->user()->nama }}</p>
        </div>
        <button onclick="document.getElementById('filterModal').showModal()"
            class="w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M11 12h4"/>
            </svg>
        </button>
    </div>

    {{-- Summary --}}
    <div class="summary-bar">
        <div class="summary-item">
            <div class="summary-val">{{ $totalTrx }}</div>
            <div class="summary-lbl">Total Trx</div>
        </div>
        <div class="summary-item">
            <div class="summary-val text-green-700">{{ $totalLunas }}</div>
            <div class="summary-lbl">Lunas</div>
        </div>
        <div class="summary-item">
            <div class="summary-val text-orange-600">{{ $totalBelum }}</div>
            <div class="summary-lbl">Parkir</div>
        </div>
    </div>

    {{-- Quick Filter Chips --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 -mx-1 px-1" style="scrollbar-width:none">
        <a href="{{ route('petugas.riwayat') }}" class="filter-chip {{ !request('status') && !request('tgl') ? 'active' : '' }}">Semua</a>
        <a href="{{ route('petugas.riwayat', ['status' => '0']) }}" class="filter-chip {{ request('status') === '0' ? 'active' : '' }}">🟡 Parkir</a>
        <a href="{{ route('petugas.riwayat', ['status' => '1']) }}" class="filter-chip {{ request('status') === '1' ? 'active' : '' }}">✅ Lunas</a>
        <a href="{{ route('petugas.riwayat', ['tgl' => now()->format('Y-m-d')]) }}" class="filter-chip {{ request('tgl') === now()->format('Y-m-d') ? 'active' : '' }}">📅 Hari Ini</a>
    </div>

    {{-- Transaksi List --}}
    @forelse($transaksis->groupBy(fn($t) => \Carbon\Carbon::parse($t->tgl)->format('Y-m-d')) as $tgl => $group)
        <div class="date-badge">
            {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}
            <span class="text-slate-400 font-normal">({{ $group->count() }} transaksi)</span>
        </div>

        @foreach($group as $trx)
        <div class="trx-item" onclick="showDetail('{{ $trx->id }}')">
            <div class="trx-icon {{ $trx->status == 1 ? 'status-lunas' : 'status-belum' }}">
                {{ $trx->status == 1 ? '✅' : '🟡' }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="plate-chip">{{ $trx->nomor_polisi }}</span>
                    <span class="badge {{ $trx->status == 1 ? 'badge-lunas' : 'badge-belum' }}">
                        {{ $trx->status == 1 ? 'LUNAS' : 'PARKIR' }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 truncate">📍 {{ $trx->lokasi->nama ?? '-' }}</div>
                <div class="flex items-center justify-between mt-1">
                    <span class="text-xs text-slate-400">
                        🕐 {{ \Carbon\Carbon::parse($trx->jam_masuk)->format('H:i') }}
                        @if($trx->jam_keluar)
                            → {{ \Carbon\Carbon::parse($trx->jam_keluar)->format('H:i') }}
                        @else
                            → sekarang
                        @endif
                    </span>
                    <span class="text-xs font-bold text-green-700">Rp {{ number_format($trx->tarif->tarif ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    @empty
        <div class="text-center py-16">
            <div class="text-5xl mb-3">📋</div>
            <p class="font-semibold text-slate-600">Belum ada transaksi</p>
            <p class="text-xs text-slate-400 mt-1">Transaksi akan muncul di sini</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($transaksis->hasPages())
    <div class="flex justify-center gap-3 mt-4">
        @if($transaksis->onFirstPage())
            <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm">← Prev</span>
        @else
            <a href="{{ $transaksis->previousPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium">← Prev</a>
        @endif
        <span class="px-4 py-2 bg-blue-700 text-white rounded-lg text-sm font-bold">{{ $transaksis->currentPage() }}</span>
        @if($transaksis->hasMorePages())
            <a href="{{ $transaksis->nextPageUrl() }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium">Next →</a>
        @else
            <span class="px-4 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm">Next →</span>
        @endif
    </div>
    @endif

</div>

{{-- Filter Modal --}}
<dialog id="filterModal" class="rounded-2xl border-0 shadow-2xl p-0 w-full max-w-sm mx-auto" style="top:auto;bottom:0;margin-bottom:0;border-radius:20px 20px 0 0;">
    <div class="bg-white rounded-t-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800">Filter Transaksi</h3>
            <button onclick="document.getElementById('filterModal').close()" class="text-slate-400 text-xl">✕</button>
        </div>
        <form method="GET" action="{{ route('petugas.riwayat') }}">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                <input type="date" name="tgl" value="{{ request('tgl') }}"
                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Sedang Parkir</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Nomor Polisi</label>
                <input type="text" name="nomor_polisi" value="{{ request('nomor_polisi') }}"
                    placeholder="Cth: B 1234 ABC"
                    class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm outline-none focus:border-blue-500 uppercase">
            </div>
            <div class="flex gap-3">
                <a href="{{ route('petugas.riwayat') }}" class="flex-1 py-3 border border-slate-200 text-slate-600 rounded-xl font-semibold text-sm text-center">Reset</a>
                <button type="submit" class="flex-1 py-3 bg-blue-700 text-white rounded-xl font-semibold text-sm">Terapkan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Detail Modal --}}
<dialog id="detailModal" class="rounded-2xl border-0 shadow-2xl p-0 w-full max-w-sm mx-auto" style="top:auto;bottom:0;margin-bottom:0;border-radius:20px 20px 0 0;">
    <div class="bg-white rounded-t-2xl p-5" id="detailContent">
        <div class="text-center py-6"><div class="spinner mx-auto mb-2" style="width:32px;height:32px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:spin 0.8s linear infinite"></div><p class="text-sm text-slate-500">Memuat...</p></div>
    </div>
</dialog>
@endsection

@push('scripts')
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
<script>
async function showDetail(id) {
    document.getElementById('detailModal').showModal();
    document.getElementById('detailContent').innerHTML = '<div class="text-center py-6"><div style="width:32px;height:32px;border:3px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 10px"></div><p class="text-sm text-slate-500">Memuat detail...</p></div>';

    try {
        const res = await fetch(`{{ url('/petugas/checkout') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        const d = await res.json();
        if (!d.transaksi) throw new Error();
        const t = d.transaksi;
        document.getElementById('detailContent').innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Detail Transaksi</h3>
                <button onclick="document.getElementById('detailModal').close()" class="text-slate-400 text-xl">✕</button>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <div class="text-center mb-3">
                    <span style="font-family:monospace;font-size:1.4rem;font-weight:800;color:#1e3a5f;letter-spacing:2px">${t.nomor_polisi}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><p class="text-slate-400 text-xs">Lokasi</p><p class="font-semibold">${t.lokasi}</p></div>
                    <div><p class="text-slate-400 text-xs">Tarif</p><p class="font-semibold">${t.tarif_nama}</p></div>
                    <div><p class="text-slate-400 text-xs">Jam Masuk</p><p class="font-semibold">${t.jam_masuk}</p></div>
                    <div><p class="text-slate-400 text-xs">Jam Keluar</p><p class="font-semibold">${t.jam_keluar || '-'}</p></div>
                    <div><p class="text-slate-400 text-xs">Durasi</p><p class="font-semibold text-orange-600">${t.durasi || '-'}</p></div>
                    <div><p class="text-slate-400 text-xs">Status</p><p class="font-semibold ${t.status == 1 ? 'text-green-600' : 'text-orange-600'}">${t.status == 1 ? '✅ Lunas' : '🟡 Parkir'}</p></div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between">
                    <span class="text-slate-500 text-sm">Total Bayar</span>
                    <span class="font-bold text-green-700">Rp ${Number(t.tarif_harga).toLocaleString('id-ID')}</span>
                </div>
            </div>
            ${t.status == 0 ? `<a href="{{ url('/petugas/scan-checkout') }}" class="block w-full py-3 bg-blue-700 text-white text-center rounded-xl font-bold text-sm">Checkout Kendaraan Ini</a>` : ''}
            <button onclick="document.getElementById('detailModal').close()" class="block w-full py-3 mt-2 border border-slate-200 text-slate-600 text-center rounded-xl font-semibold text-sm">Tutup</button>`;
    } catch(e) {
        document.getElementById('detailContent').innerHTML = '<p class="text-center text-slate-500 py-4">Gagal memuat detail</p>';
    }
}
</script>
@endpush
