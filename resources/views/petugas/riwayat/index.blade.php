@extends('layouts.petugas')

@section('title', 'Riwayat Transaksi')

@push('styles')
<style>
.riw-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
}
.riw-header h1 { font-size: 1.1rem; font-weight: 800; color: var(--charcoal); }
.riw-header p  { font-size: .75rem; color: var(--gray-400); margin-top: 1px; }
.riw-header .btn-filter {
    width: 38px; height: 38px; border-radius: 11px;
    background: var(--white); border: 1px solid var(--gray-200);
    display: flex; align-items: center; justify-content: center;
    color: var(--gray-600); cursor: pointer; position: relative;
}
.btn-filter .dot {
    position: absolute; top: 6px; right: 6px; width: 8px; height: 8px;
    background: var(--blue-light); border-radius: 50%;
    border: 2px solid var(--white);
}

.summary-bar {
    display: grid; grid-template-columns: 1fr 1fr 1fr;
    gap: 8px; margin-bottom: 16px;
}
.summary-item {
    background: var(--white); border-radius: 12px;
    border: 1px solid var(--gray-100);
    padding: 12px 10px; text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.summary-val { font-size: 1.25rem; font-weight: 800; line-height: 1; }
.summary-lbl { font-size: .66rem; color: var(--gray-400); margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }

.chip-row {
    display: flex; gap: 8px; overflow-x: auto; padding-bottom: 8px;
    margin-bottom: 12px; scrollbar-width: none;
}
.chip-row::-webkit-scrollbar { display: none; }
.chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 20px;
    font-size: .76rem; font-weight: 700;
    border: 1.5px solid var(--gray-200);
    background: var(--white); color: var(--gray-500);
    white-space: nowrap;
}
.chip.active {
    background: var(--navy); color: var(--white); border-color: var(--navy);
}

.date-badge {
    font-size: .72rem; font-weight: 800; color: var(--gray-500);
    text-transform: uppercase; letter-spacing: .5px;
    padding: 10px 4px 6px; display: flex; align-items: center; gap: 6px;
}
.date-badge .count { color: var(--gray-400); font-weight: 600; }

.trx-item {
    background: var(--white); border-radius: 14px;
    border: 1px solid var(--gray-100);
    padding: 12px 14px; margin-bottom: 8px;
    display: flex; align-items: center; gap: 12px;
    transition: all .15s; color: inherit;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.trx-item:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.06); }

.trx-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; flex-shrink: 0;
}
.trx-icon.lunas { background: var(--success-soft); color: var(--success); }
.trx-icon.belum { background: var(--warning-soft); color: var(--warning); }

.trx-body { flex: 1; min-width: 0; }
.trx-top {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 4px; gap: 8px;
}
.trx-plate {
    font-family: 'Courier New', monospace;
    font-weight: 800; font-size: .92rem; letter-spacing: 1px;
    color: var(--charcoal);
}
.trx-loc {
    font-size: .72rem; color: var(--gray-500);
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    display: flex; align-items: center; gap: 4px;
}
.trx-mid {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 4px;
}
.trx-time { font-size: .7rem; color: var(--gray-400); }
.trx-money { font-size: .82rem; font-weight: 800; color: var(--success); }
.trx-money.belum { color: var(--gray-400); }

.empty-state {
    text-align: center; padding: 50px 16px; color: var(--gray-400);
    background: var(--white); border-radius: 14px;
    border: 1.5px dashed var(--gray-200);
}
.empty-state .ic { font-size: 3rem; margin-bottom: 10px; }
.empty-state .ttl { font-weight: 700; color: var(--gray-600); margin-bottom: 4px; font-size: .92rem; }
.empty-state .sb { font-size: .78rem; }

/* Pagination */
.pagi {
    display: flex; justify-content: center; gap: 8px;
    margin-top: 16px; align-items: center;
}
.pagi a, .pagi span {
    padding: 9px 14px; border-radius: 10px; font-size: .82rem; font-weight: 700;
    text-decoration: none;
}
.pagi .cur { background: var(--blue-light); color: var(--white); }
.pagi .lnk { background: var(--white); border: 1px solid var(--gray-200); color: var(--gray-700); }
.pagi .dis { background: var(--gray-100); color: var(--gray-400); }

/* Filter dialog */
.filter-dialog { width: 100%; max-width: 460px; }

/* Detail dialog */
.detail-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 18px; border-bottom: 1px solid var(--gray-100);
}
.detail-head h3 { font-size: 1rem; font-weight: 800; color: var(--charcoal); }
.detail-body { padding: 16px 18px; }
.detail-plate-bx {
    background: linear-gradient(135deg, var(--navy), var(--blue-light));
    color: #fff; border-radius: 14px;
    text-align: center; padding: 18px;
    margin-bottom: 14px;
}
.detail-plate-bx .lbl { font-size: .66rem; opacity: .8; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.detail-plate-bx .val { font-family: 'Courier New', monospace; font-size: 1.7rem; font-weight: 800; letter-spacing: 2px; margin-top: 4px; }
.detail-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;
}
.detail-grid .it {
    background: var(--gray-50); border-radius: 10px; padding: 10px 12px;
}
.detail-grid .it .l { font-size: .68rem; color: var(--gray-500); font-weight: 600; margin-bottom: 3px; }
.detail-grid .it .v { font-size: .85rem; font-weight: 700; color: var(--charcoal); }
.detail-total {
    background: var(--success-soft); border-radius: 12px;
    padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px;
}
.detail-total .l { font-size: .82rem; color: #047857; font-weight: 700; }
.detail-total .v { font-size: 1.15rem; color: #065f46; font-weight: 800; }
</style>
@endpush

@section('content')

<div class="riw-header">
    <div>
        <h1>Riwayat Parkir</h1>
        <p>{{ auth()->user()->nama }}</p>
    </div>
    <button onclick="document.getElementById('filterModal').showModal()" class="btn-filter">
        <i class="fa-solid fa-sliders"></i>
        @if(request('tgl') || request('nomor_polisi'))
        <span class="dot"></span>
        @endif
    </button>
</div>

<div class="summary-bar">
    <div class="summary-item">
        <div class="summary-val" style="color:var(--navy);">{{ $totalTrx }}</div>
        <div class="summary-lbl">Total</div>
    </div>
    <div class="summary-item">
        <div class="summary-val" style="color:var(--success);">{{ $totalLunas }}</div>
        <div class="summary-lbl">Lunas</div>
    </div>
    <div class="summary-item">
        <div class="summary-val" style="color:var(--warning);">{{ $totalBelum }}</div>
        <div class="summary-lbl">Parkir</div>
    </div>
</div>

<div class="chip-row">
    <a href="{{ route('petugas.riwayat') }}" class="chip {{ !request('status') && !request('tgl') ? 'active' : '' }}">
        <i class="fa-solid fa-list"></i> Semua
    </a>
    <a href="{{ route('petugas.riwayat', ['status' => '0']) }}" class="chip {{ request('status') === '0' ? 'active' : '' }}">
        <i class="fa-solid fa-clock"></i> Parkir
    </a>
    <a href="{{ route('petugas.riwayat', ['status' => '1']) }}" class="chip {{ request('status') === '1' ? 'active' : '' }}">
        <i class="fa-solid fa-circle-check"></i> Lunas
    </a>
    <a href="{{ route('petugas.riwayat', ['tgl' => now()->format('Y-m-d')]) }}" class="chip {{ request('tgl') === now()->format('Y-m-d') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-day"></i> Hari Ini
    </a>
</div>

@forelse($transaksis->groupBy(fn($t) => \Carbon\Carbon::parse($t->tgl)->format('Y-m-d')) as $tgl => $group)
    <div class="date-badge">
        <i class="fa-solid fa-calendar"></i>
        {{ \Carbon\Carbon::parse($tgl)->translatedFormat('l, d F Y') }}
        <span class="count">· {{ $group->count() }} transaksi</span>
    </div>

    @foreach($group as $trx)
    <div class="trx-item" onclick="showDetail({{ $trx->id }})" style="cursor:pointer;">
        <div class="trx-icon {{ $trx->status == 1 ? 'lunas' : 'belum' }}">
            <i class="fa-solid fa-{{ $trx->status == 1 ? 'circle-check' : 'clock' }}"></i>
        </div>
        <div class="trx-body">
            <div class="trx-top">
                <span class="trx-plate">{{ $trx->nomor_polisi }}</span>
                <span class="badge {{ $trx->status == 1 ? 'badge-success' : 'badge-warning' }}">
                    {{ $trx->status == 1 ? 'LUNAS' : 'PARKIR' }}
                </span>
            </div>
            <div class="trx-loc">
                <i class="fa-solid fa-location-dot"></i>
                {{ $trx->lokasi->nama ?? '-' }} · {{ $trx->tarif->nama ?? '-' }}
            </div>
            <div class="trx-mid">
                <span class="trx-time">
                    <i class="fa-regular fa-clock"></i>
                    {{ \Carbon\Carbon::parse($trx->jam_masuk)->format('H:i') }}
                    @if($trx->jam_keluar)
                        → {{ \Carbon\Carbon::parse($trx->jam_keluar)->format('H:i') }}
                    @else
                        → sekarang
                    @endif
                </span>
                <span class="trx-money {{ $trx->status == 1 ? '' : 'belum' }}">
                    Rp {{ number_format($trx->tarif->tarif ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
    @endforeach
@empty
    <div class="empty-state">
        <div class="ic">📋</div>
        <div class="ttl">Belum ada transaksi</div>
        <div class="sb">Transaksi akan muncul setelah ada kendaraan masuk</div>
    </div>
@endforelse

@if($transaksis->hasPages())
<div class="pagi">
    @if($transaksis->onFirstPage())
        <span class="dis"><i class="fa-solid fa-chevron-left"></i></span>
    @else
        <a href="{{ $transaksis->previousPageUrl() }}" class="lnk"><i class="fa-solid fa-chevron-left"></i></a>
    @endif
    <span class="cur">Hal {{ $transaksis->currentPage() }} / {{ $transaksis->lastPage() }}</span>
    @if($transaksis->hasMorePages())
        <a href="{{ $transaksis->nextPageUrl() }}" class="lnk"><i class="fa-solid fa-chevron-right"></i></a>
    @else
        <span class="dis"><i class="fa-solid fa-chevron-right"></i></span>
    @endif
</div>
@endif

{{-- Filter Modal --}}
<dialog id="filterModal" class="filter-dialog">
    <div style="padding:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <h3 style="font-size:1rem;font-weight:800;color:var(--charcoal);">Filter Transaksi</h3>
            <button onclick="document.getElementById('filterModal').close()" style="border:none;background:var(--gray-100);border-radius:10px;width:32px;height:32px;cursor:pointer;color:var(--gray-500);">✕</button>
        </div>
        <form method="GET" action="{{ route('petugas.riwayat') }}">
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tgl" value="{{ request('tgl') }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Sedang Parkir</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Polisi</label>
                <input type="text" name="nomor_polisi" value="{{ request('nomor_polisi') }}"
                       placeholder="cth: BA 1234 AB"
                       class="form-control"
                       style="text-transform:uppercase;font-family:'Courier New',monospace;">
            </div>
            <div style="display:flex;gap:8px;">
                <a href="{{ route('petugas.riwayat') }}" class="btn btn-secondary" style="flex:1;">Reset</a>
                <button type="submit" class="btn btn-primary" style="flex:1;">Terapkan</button>
            </div>
        </form>
    </div>
</dialog>

{{-- Detail Modal --}}
<dialog id="detailModal">
    <div id="detailContent">
        <div style="padding:40px;text-align:center;">
            <div class="spinner" style="margin:0 auto 10px;"></div>
            <p style="font-size:.85rem;color:var(--gray-500);">Memuat...</p>
        </div>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
async function showDetail(id) {
    document.getElementById('detailModal').showModal();
    document.getElementById('detailContent').innerHTML = `
        <div style="padding:40px;text-align:center;">
            <div class="spinner" style="margin:0 auto 10px;"></div>
            <p style="font-size:.85rem;color:var(--gray-500);">Memuat detail...</p>
        </div>`;

    try {
        const res = await fetch(`{{ url('/petugas/checkout') }}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const d = await res.json();
        if (!d.transaksi) throw new Error();
        const t = d.transaksi;
        const statusBadge = t.status == 1
            ? '<span class="badge badge-success">LUNAS</span>'
            : '<span class="badge badge-warning">PARKIR</span>';

        document.getElementById('detailContent').innerHTML = `
            <div class="detail-head">
                <h3>Detail Transaksi</h3>
                <button onclick="document.getElementById('detailModal').close()" style="border:none;background:var(--gray-100);border-radius:10px;width:32px;height:32px;cursor:pointer;color:var(--gray-500);">✕</button>
            </div>
            <div class="detail-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <span style="font-size:.72rem;color:var(--gray-500);font-weight:600;">No. Ref: <b style="color:var(--charcoal);">${t.nomor_referensi || '-'}</b></span>
                    ${statusBadge}
                </div>

                <div class="detail-plate-bx">
                    <div class="lbl">Plat Nomor</div>
                    <div class="val">${t.nomor_polisi}</div>
                </div>

                <div class="detail-grid">
                    <div class="it"><div class="l">📍 Lokasi</div><div class="v">${t.lokasi}</div></div>
                    <div class="it"><div class="l">🏷️ Tarif</div><div class="v">${t.tarif_nama}</div></div>
                    <div class="it"><div class="l">🕐 Jam Masuk</div><div class="v">${t.jam_masuk}</div></div>
                    <div class="it"><div class="l">🕗 Jam Keluar</div><div class="v">${t.jam_keluar || '-'}</div></div>
                    <div class="it" style="grid-column:1/-1;"><div class="l">⏱ Durasi</div><div class="v" style="color:var(--warning);">${t.durasi || '-'}</div></div>
                    ${t.metode_nama ? `<div class="it" style="grid-column:1/-1;"><div class="l">💳 Metode Bayar</div><div class="v">${t.metode_nama}</div></div>` : ''}
                </div>

                <div class="detail-total">
                    <span class="l">Total Biaya</span>
                    <span class="v">Rp ${Number(t.tarif_harga).toLocaleString('id-ID')}</span>
                </div>

                ${t.status == 0
                    ? `<a href="{{ url('/petugas/scan-checkout') }}?plate=${encodeURIComponent(t.nomor_polisi)}" class="btn btn-success btn-full" style="padding:13px;"><i class="fa-solid fa-circle-check"></i> Checkout Kendaraan</a>`
                    : ''}
                <button onclick="document.getElementById('detailModal').close()" class="btn btn-secondary btn-full" style="margin-top:8px;">Tutup</button>
            </div>`;
    } catch(e) {
        document.getElementById('detailContent').innerHTML = `
            <div style="padding:40px;text-align:center;">
                <div style="font-size:2.5rem;">⚠️</div>
                <p style="font-size:.85rem;color:var(--gray-500);margin-top:10px;">Gagal memuat detail</p>
                <button onclick="document.getElementById('detailModal').close()" class="btn btn-secondary" style="margin-top:14px;">Tutup</button>
            </div>`;
    }
}
</script>
@endpush
