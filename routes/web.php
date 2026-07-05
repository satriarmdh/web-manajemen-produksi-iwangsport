<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner\UserManagementController;
use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\StandardBaselineProduksiController;
use App\Http\Controllers\Admin\PergerakanStokController;
use App\Http\Controllers\Admin\PemasukanBahanController;
use App\Http\Controllers\Admin\PengeluaranBahanController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PerintahProduksiController;
use App\Http\Controllers\Owner\PerintahProduksiOwnerController;
use App\Http\Controllers\Produksi\DashboardProduksiController;
use App\Http\Controllers\Produksi\PerintahProduksiKaryawanController;
use App\Http\Controllers\Produksi\PotongController;
use App\Http\Controllers\Produksi\InputHasilPekerjaanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () { 
        return view('owner.dashboard'); 
    })->name('dashboard');

    Route::get('/persetujuan-workorder', function () { 
        return view('owner.persetujuan-workorder'); 
    })->name('persetujuan-workorder');

    Route::resource('users', UserManagementController::class)->except(['show']);

    // Rute Perintah Produksi untuk Approval Owner (BELEUM DIPAKAI)
    Route::get('perintah-produksi', [PerintahProduksiOwnerController::class, 'index'])->name('perintah-produksi.index');
    Route::post('perintah-produksi/{perintahProduksi}/approve', [PerintahProduksiOwnerController::class, 'approve'])->name('perintah-produksi.approve');
    Route::post('perintah-produksi/{perintahProduksi}/reject', [PerintahProduksiOwnerController::class, 'reject'])->name('perintah-produksi.reject');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');    

    // Rute pengelolaan Bahan Baku
    Route::resource('bahan-baku', BahanBakuController::class)->except(['create', 'show', 'edit']);

    // AJAX route untuk generate kode bahan baku
    Route::get('/bahan-baku/generate-kode/{kategori}', [BahanBakuController::class, 'generateKode'])
        ->name('bahan-baku.generate-kode');

    // Rute pengelolaan Produk
    Route::resource('produk', ProdukController::class)->except(['create', 'show', 'edit']);

    // Rute pengelolaan Supplier
    Route::resource('supplier', SupplierController::class)->except(['create', 'edit']);

    // Rute pengelolaan Pelanggan
    Route::resource('pelanggan', PelangganController::class)->except(['create', 'edit']);

    // Rute pengelolaan Standard Baseline Produksi
    Route::resource('standard-baseline-produksi', StandardBaselineProduksiController::class)->except(['create', 'show', 'edit']);

    // Rute Pergerakan Stok Bahan Baku
    Route::get('pergerakan-stok', [PergerakanStokController::class, 'index'])->name('pergerakan-stok.index');

    // Rute Pemasukan Bahan Baku (Stok Masuk)
    Route::resource('pemasukan-bahan', PemasukanBahanController::class)->except(['create', 'show', 'edit']);

    // Rute Pengeluaran Bahan Baku (Stok Keluar)
    Route::resource('pengeluaran-bahan', PengeluaranBahanController::class)->except(['create', 'show', 'edit']);

    // Rute Perintah Produksi
    Route::resource('perintah-produksi', PerintahProduksiController::class);
    Route::post('perintah-produksi/{perintahProduksi}/selesai', [PerintahProduksiController::class, 'selesai'])->name('perintah-produksi.selesai');
    Route::get('perintah-produksi/{perintahProduksi}/cetak-pdf', [PerintahProduksiController::class, 'cetakPdf'])->name('perintah-produksi.cetak-pdf');
});

Route::middleware(['auth', 'role:potong,jahit,finishing'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', [DashboardProduksiController::class, 'index'])->name('dashboard');
    Route::get('/perintah-produksi', [PerintahProduksiKaryawanController::class, 'index'])->name('perintah-produksi.index');
    Route::get('/perintah-produksi/{perintahProduksi}', [PerintahProduksiKaryawanController::class, 'show'])->name('perintah-produksi.show');

    Route::get('/input-hasil', function () {
        return view('produksi.dashboard');
    })->name('input-hasil.index');
    Route::post('/input-hasil', [InputHasilPekerjaanController::class, 'store'])->name('input-hasil.store');

    Route::get('/ajuan-pengambilan', function () {
        return view('produksi.dashboard');
    })->name('ajuan-pengambilan.index');

    Route::post('/potong/{detail}/input-hasil', [PotongController::class, 'inputHasil'])->name('potong.input-hasil');
});
