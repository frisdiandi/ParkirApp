<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\{DashboardController, LokasiController, TarifController, MetodePembayaranController, PetugasController, TransaksiController};
use App\Http\Controllers\Petugas\ParkController;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin & Pimpinan Routes (level 1 & 3)
Route::prefix('admin')->middleware(['auth', 'level:1,3'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master
    Route::resource('lokasi', LokasiController::class)->except(['show']);
    Route::resource('tarif', TarifController::class)->except(['show']);
    Route::get('metode', [MetodePembayaranController::class, 'index'])->name('metode.index');
    Route::post('metode', [MetodePembayaranController::class, 'store'])->name('metode.store');
    Route::put('metode/{metode}', [MetodePembayaranController::class, 'update'])->name('metode.update');
    Route::delete('metode/{metode}', [MetodePembayaranController::class, 'destroy'])->name('metode.destroy');

    // Data Petugas
    Route::resource('petugas', PetugasController::class)->except(['show']);

    // Transaksi (view only)
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
});

// Petugas Routes (level 2)
Route::prefix('petugas')->middleware(['auth', 'level:2'])->name('petugas.')->group(function () {
    Route::get('/dashboard', [ParkController::class, 'dashboard'])->name('dashboard');
    Route::get('/tambah', [ParkController::class, 'formTambah'])->name('tambah');
    Route::post('/masuk', [ParkController::class, 'simpanMasuk'])->name('masuk');
    Route::get('/riwayat', [ParkController::class, 'riwayat'])->name('riwayat');
    Route::get('/scan-checkout', [ParkController::class, 'scanCheckout'])->name('scan-checkout');
    Route::get('/cari-checkout', [ParkController::class, 'cariCheckout'])->name('cari-checkout');
    Route::get('/checkout/{transaksi}', [ParkController::class, 'detailCheckout'])->name('checkout');
    Route::post('/checkout/{transaksi}', [ParkController::class, 'prosesCheckout'])->name('proses-checkout');
    Route::get('/profile', [ParkController::class, 'profile'])->name('profile');
    Route::post('/scan-plate', [ParkController::class, 'scanPlate'])->name('scan-plate');
});

// PWA manifest & service worker
Route::get('/manifest.json', fn() => response()->file(public_path('manifest.json'), ['Content-Type' => 'application/manifest+json']));
Route::get('/sw.js', fn() => response()->file(public_path('sw.js'), ['Content-Type' => 'application/javascript']));
