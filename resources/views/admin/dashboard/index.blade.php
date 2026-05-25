@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<style>
.grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
.grid-2 { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 24px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }

.stat-card .stat-card-icon { background: var(--clr-bg, #dbeafe); color: var(--clr, var(--blue-light)); }

.chart-wrap { position: relative; height: 260px; }
canvas { max-height: 260px; }

.lokasi-bar-item { margin-bottom: 14px; }
.lokasi-bar-label { display: flex; justify-content: space-between; font-size: .82rem; margin-bottom: 5px; font-weight: 500; }
.bar-track { height: 8px; background: var(--gray-100); border-radius: 99px; overflow: hidden; }
.bar-fill  { height: 100%; background: linear-gradient(90deg, var(--blue-light), var(--navy-mid)); border-radius: 99px; transition: width 1s ease; }

.activity-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid var(--gray-100);
}
.activity-item:last-child { border-bottom: none; }
.act-icon {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.act-info { flex: 1; min-width: 0; }
.act-title { font-size: .85rem; font-weight: 600; color: var(--charcoal); }
.act-sub   { font-size: .75rem; color: var(--gray-400); margin-top: 2px; }
.act-right { text-align: right; flex-shrink: 0; }
.act-time  { font-size: .72rem; color: var(--gray-400); }
.act-amount { font-size: .82rem; font-weight: 700; }

@media (max-width: 1200px) { .grid-4 { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 900px)  { .grid-2 { grid-template-columns: 1fr; } .grid-3 { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px)  { .grid-4 { grid-template-columns: 1fr 1fr; } .grid-3 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<!-- Stat Cards -->
<div class="grid-4">
    <div class="stat-card" style="--clr-bg:#dbeafe;--clr:#1e50a0;">
        <div class="stat-card-icon"><i class="fa-solid fa-car"></i></div>
        <div class="stat-card-value">{{ number_format($stats['transaksi_hari_ini']) }}</div>
        <div class="stat-card-label">Transaksi Hari Ini</div>
        <div class="stat-card-trend" style="color:var(--blue-light)">
            <i class="fa-solid fa-calendar-day"></i> {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>

    <div class="stat-card" style="--clr-bg:#dcfce7;--clr:#15803d;">
        <div class="stat-card-icon"><i class="fa-solid fa-wallet"></i></div>
        <div class="stat-card-value" style="font-size:1.35rem;">Rp {{ number_format($stats['pendapatan_hari_ini'],0,',','.') }}</div>
        <div class="stat-card-label">Pendapatan Hari Ini</div>
        <div class="stat-card-trend" style="color:var(--success)">
            <i class="fa-solid fa-check-circle"></i> Hanya lunas
        </div>
    </div>

    <div class="stat-card" style="--clr-bg:#fef3c7;--clr:#d97706;">
        <div class="stat-card-icon"><i class="fa-solid fa-clock"></i></div>
        <div class="stat-card-value">{{ number_format($stats['kendaraan_parkir']) }}</div>
        <div class="stat-card-label">Kendaraan Sedang Parkir</div>
        <div class="stat-card-trend" style="color:var(--warning)">
            <i class="fa-solid fa-circle-dot"></i> Belum checkout
        </div>
    </div>

    <div class="stat-card" style="--clr-bg:#ede9fe;--clr:#7c3aed;">
        <div class="stat-card-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-card-value">{{ $stats['total_petugas'] }}</div>
        <div class="stat-card-label">Petugas Aktif</div>
        <div class="stat-card-trend" style="color:#7c3aed">
            <i class="fa-solid fa-map-marker-alt"></i> {{ $stats['total_lokasi'] }} Lokasi
        </div>
    </div>
</div>

<!-- Chart + Lokasi -->
<div class="grid-2">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-chart-line" style="color:var(--blue-light);margin-right:6px"></i>Transaksi 7 Hari Terakhir</h4>
            <span class="badge-pill badge-info">{{ now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="chartTransaksi"></canvas>
            </div>
        </div>
    </div>

    <!-- Per Lokasi -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-map-location-dot" style="color:var(--blue-light);margin-right:6px"></i>Kendaraan per Lokasi</h4>
            <span style="font-size:.75rem;color:var(--gray-400)">Hari ini</span>
        </div>
        <div class="card-body">
            @php $maxLok = $perLokasi->max('total') ?: 1; @endphp
            @forelse($perLokasi as $pl)
            <div class="lokasi-bar-item">
                <div class="lokasi-bar-label">
                    <span>{{ $pl->lokasi?->nama ?? 'N/A' }}</span>
                    <strong>{{ $pl->total }}</strong>
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ ($pl->total/$maxLok)*100 }}%"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px 0;color:var(--gray-400);">
                <i class="fa-solid fa-parking" style="font-size:2rem;margin-bottom:8px;display:block;"></i>
                Belum ada data hari ini
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Transactions + Monthly Summary -->
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-receipt" style="color:var(--blue-light);margin-right:6px"></i>Transaksi Terbaru</h4>
            <a href="{{ route('admin.transaksi.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding-top:8px;padding-bottom:8px;">
            @forelse($transaksiTerbaru as $t)
            <div class="activity-item">
                <div class="act-icon" style="background:{{ $t->status ? '#dcfce7' : '#fef3c7' }};color:{{ $t->status ? '#15803d' : '#d97706' }}">
                    <i class="fa-solid fa-{{ $t->status ? 'circle-check' : 'clock' }}"></i>
                </div>
                <div class="act-info">
                    <div class="act-title">{{ $t->nomor_polisi }}</div>
                    <div class="act-sub">{{ $t->lokasi?->nama }} · {{ $t->tarif?->nama }}</div>
                </div>
                <div class="act-right">
                    <div class="act-amount" style="color:{{ $t->status ? 'var(--success)' : 'var(--warning)' }}">
                        {{ $t->tarif?->tarif_format }}
                    </div>
                    <div class="act-time">{{ $t->jam_masuk }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--gray-400);">Belum ada transaksi</div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4><i class="fa-solid fa-chart-bar" style="color:var(--blue-light);margin-right:6px"></i>Pendapatan Bulan Ini</h4>
        </div>
        <div class="card-body">
            <div style="text-align:center;padding:20px 0;">
                <div style="font-size:2rem;font-weight:800;color:var(--navy);">
                    Rp {{ number_format($stats['pendapatan_bulan_ini'],0,',','.') }}
                </div>
                <div style="font-size:.85rem;color:var(--gray-400);margin-top:4px;">Total bulan {{ now()->format('F Y') }}</div>
            </div>
            <div style="border-top:1px solid var(--gray-100);padding-top:16px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:.85rem;color:var(--gray-500)">Total Transaksi Bulan Ini</span>
                    <strong>{{ number_format($stats['transaksi_bulan_ini']) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                    <span style="font-size:.85rem;color:var(--gray-500)">Rata-rata per Hari</span>
                    <strong>Rp {{ now()->day > 0 ? number_format($stats['pendapatan_bulan_ini'] / now()->day, 0, ',', '.') : 0 }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-size:.85rem;color:var(--gray-500)">Lokasi Aktif</span>
                    <strong>{{ $stats['total_lokasi'] }} lokasi</strong>
                </div>
            </div>
            <div style="margin-top:16px;">
                <div class="chart-wrap" style="height:120px;">
                    <canvas id="chartPendapatan"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
const grafikData = @json($grafikData);
const labels    = grafikData.map(d => d.tgl);
const dataTrx   = grafikData.map(d => d.transaksi);
const dataPend  = grafikData.map(d => d.pendapatan);

// Transaksi chart
new Chart(document.getElementById('chartTransaksi'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: dataTrx,
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: 'rgba(37,99,235,0.8)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, stepSize: 1 } }
        }
    }
});

// Pendapatan sparkline
new Chart(document.getElementById('chartPendapatan'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            data: dataPend,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            tension: 0.4, fill: true,
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { display: false },
            y: { display: false }
        }
    }
});
</script>
@endpush
