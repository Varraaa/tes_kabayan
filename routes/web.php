<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// Login/Logout
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 2. auth routes (harus login)
Route::middleware('auth')->group(function () {

    //akses admin dan operator
    Route::middleware('role:admin,operator')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        //transaksi
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/masuk', [TransaksiController::class, 'createMasuk'])->name('transaksi.masuk');
        Route::post('/transaksi/masuk', [TransaksiController::class, 'storeMasuk'])->name('transaksi.masuk.store');
        Route::get('/transaksi/jual', [TransaksiController::class, 'createJual'])->name('transaksi.jual');
        Route::post('/transaksi/jual', [TransaksiController::class, 'storeJual'])->name('transaksi.jual.store');
        Route::get('/transaksi/transfer', [TransaksiController::class, 'createTransfer'])->name('transaksi.transfer');
        Route::post('/transaksi/transfer', [TransaksiController::class, 'storeTransfer'])->name('transaksi.transfer.store');
        Route::post('/transaksi/{transaksi}/batal', [TransaksiController::class, 'batalkan'])->name('transaksi.batal');
    });

    //akses khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('gudang', GudangController::class);
        Route::resource('pelanggan', PelangganController::class);
        Route::resource('barang', BarangController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');
    });
});
