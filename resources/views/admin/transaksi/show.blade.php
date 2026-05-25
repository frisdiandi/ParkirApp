@extends('layouts.admin')

@section('title', 'Detail Transaksi')
@section('breadcrumb', 'Data Transaksi / Detail')

@section('content')
<div class="max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.transaksi.index') }}"
            class="w-9 h-9 rounded-lg border border-slate-200 bg-white flex items-center justify-center hover:bg-slate-50 transition">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Detail Transaksi</h1>
            <p class="text-sm text-slate-500">{{ $transaksi->nomor_referensi }}</p>
        </div>
        <div class="ml-auto">
            @if($transaksi->status == 1)
                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Lunas
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-bold">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span> Sedang Parkir
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Kendaraan Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="bg-gradient-to-r from-[#1e3a5f] to-[#2563eb] px-6 py-4">
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-wide mb-1">Nomor Kendaraan</p>
                    <div class="flex items-center justify-between">
                        <p class="text-white font-black text-3xl tracking-widest font-mono">{{ $transaksi->nomor_polisi }}</p>
                        <div class="text-right">
                            <p class="text-blue-200 text-xs">Referensi</p>
                            <p class="text-white font-mono text-sm font-bold">{{ $transaksi->nomor_referensi }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Lokasi Parkir</p>
                        <p class="font-semibold text-slate-800">{{ $transaksi->lokasi->nama ?? '-' }}</p>
                        @if($transaksi->lokasi?->koordinat)
                            <a href="https://maps.google.com/?q={{ $transaksi->lokasi->koordinat }}" target="_blank"
                                class="text-xs text-blue-600 hover:underline">📍 Lihat di Maps</a>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Jenis Kendaraan</p>
                        <p class="font-semibold text-slate-800">{{ $transaksi->tarif->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Tanggal</p>
                        <p class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($transaksi->tgl)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Metode Bayar</p>
                        <p class="font-semibold text-slate-800">{{ $transaksi->metodePembayaran->nama ?? ($transaksi->status == 0 ? '-' : 'N/A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Waktu Parkir --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-700 mb-4 text-sm uppercase tracking-wide">⏱ Waktu Parkir</h3>
                <div class="flex items-center gap-0">
                    <div class="flex-1 text-center">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl mx-auto mb-2">🚗</div>
                        <p class="text-xs text-slate-400">Jam Masuk</p>
                        <p class="font-black text-blue-700 text-xl">{{ \Carbon\Carbon::parse($transaksi->jam_masuk)->format('H:i') }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transaksi->jam_masuk)->translatedFormat('d F Y') }}</p>
                    </div>

                    <div class="flex-1 relative flex items-center justify-center">
                        <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 border-t-2 border-dashed border-slate-200"></div>
                        <div class="relative bg-white px-2">
                            <div class="bg-slate-100 rounded-full px-3 py-1 text-xs font-bold text-slate-600">
                                {{ $transaksi->durasi ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 text-center">
                        @if($transaksi->jam_keluar)
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center text-2xl mx-auto mb-2">✅</div>
                            <p class="text-xs text-slate-400">Jam Keluar</p>
                            <p class="font-black text-green-700 text-xl">{{ \Carbon\Carbon::parse($transaksi->jam_keluar)->format('H:i') }}</p>
                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transaksi->jam_keluar)->translatedFormat('d F Y') }}</p>
                        @else
                            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl mx-auto mb-2 animate-pulse">🟡</div>
                            <p class="text-xs text-slate-400">Keluar</p>
                            <p class="font-black text-orange-600 text-sm">Sedang Parkir</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Petugas --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-700 mb-4 text-sm uppercase tracking-wide">👷 Petugas</h3>
                <div class="flex items-center gap-4">
                    @if($transaksi->petugas?->foto)
                        <img src="{{ asset('storage/' . $transaksi->petugas->foto) }}" alt="foto petugas"
                            class="w-14 h-14 rounded-full object-cover border-2 border-blue-200">
                    @else
                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#1e3a5f] to-[#2563eb] flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr($transaksi->petugas?->user?->nama ?? 'P', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-bold text-slate-800">{{ $transaksi->petugas?->user?->nama ?? '-' }}</p>
                        <p class="text-sm text-slate-500">@{{ $transaksi->petugas?->user?->username ?? '-' }}</p>
                        <p class="text-xs text-slate-400 mt-1">📞 {{ $transaksi->petugas?->user?->contact ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-5">

            {{-- Ringkasan Bayar --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-700 mb-4 text-sm uppercase tracking-wide">💰 Ringkasan Biaya</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tarif {{ $transaksi->tarif->nama ?? '' }}</span>
                        <span class="font-semibold">Rp {{ number_format($transaksi->tarif->tarif ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between">
                        <span class="font-bold text-slate-700">Total</span>
                        <span class="font-black text-green-700 text-lg">Rp {{ number_format($transaksi->tarif->tarif ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Metode</span>
                        <span class="font-semibold">{{ $transaksi->metodePembayaran->nama ?? '-' }}</span>
                    </div>
                </div>

                @if($transaksi->status == 0)
                <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-xl text-xs text-orange-700 font-semibold text-center">
                    ⏳ Belum dibayar — Masih parkir
                </div>
                @else
                <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-700 font-semibold text-center">
                    ✅ Pembayaran lunas
                </div>
                @endif
            </div>

            {{-- Tarif Info --}}
            @if($transaksi->tarif?->foto)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wide">🏷️ Tarif</h3>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('storage/' . $transaksi->tarif->foto) }}" alt="tarif"
                        class="w-14 h-14 rounded-xl object-cover">
                    <div>
                        <p class="font-bold text-slate-800">{{ $transaksi->tarif->nama }}</p>
                        <p class="text-green-700 font-bold">Rp {{ number_format($transaksi->tarif->tarif, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Timestamps --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h3 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wide">🕐 Riwayat</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 mt-1 flex-shrink-0"></div>
                        <div>
                            <p class="font-semibold text-slate-700">Kendaraan masuk</p>
                            <p class="text-slate-400">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($transaksi->jam_keluar)
                    <div class="flex gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 mt-1 flex-shrink-0"></div>
                        <div>
                            <p class="font-semibold text-slate-700">Kendaraan keluar & bayar</p>
                            <p class="text-slate-400">{{ \Carbon\Carbon::parse($transaksi->updated_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Print / Back --}}
            <div class="space-y-2">
                <button onclick="window.print()" class="w-full py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-2">
                    🖨️ Cetak
                </button>
                <a href="{{ route('admin.transaksi.index') }}" class="w-full py-2.5 border border-blue-200 rounded-xl text-sm font-semibold text-blue-700 hover:bg-blue-50 transition flex items-center justify-center gap-2">
                    ← Kembali ke Daftar
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
