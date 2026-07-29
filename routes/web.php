<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner\UserManagementController;
use App\Http\Controllers\Owner\DashboardOwnerController;
use App\Http\Controllers\Owner\MutasiBahanBakuOwnerController;
use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\StandardBaselineProduksiController;
use App\Http\Controllers\Admin\PergerakanStokController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PerintahProduksiController;
use App\Http\Controllers\Admin\PenerimaanHasilProduksiController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Owner\PerintahProduksiOwnerController;
use App\Http\Controllers\Produksi\DashboardProduksiController;
use App\Http\Controllers\Produksi\PerintahProduksiKaryawanController;
use App\Http\Controllers\Produksi\PotongController;
use App\Http\Controllers\Produksi\InputHasilPekerjaanController;
use App\Http\Controllers\Produksi\ProdukCacatController;
use App\Http\Controllers\Produksi\AjuanPengambilanProduksiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardOwnerController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales-trend', [DashboardOwnerController::class, 'salesTrend'])->name('dashboard.sales-trend');
    Route::get('/inventori', [DashboardOwnerController::class, 'inventori'])->name('inventori');
    Route::get('/mutasi-bahan-baku', [MutasiBahanBakuOwnerController::class, 'mutasiBahanBaku'])->name('mutasi-bahan-baku.index');
    Route::get('/mutasi-bahan-baku/{pergerakanStok}', [MutasiBahanBakuOwnerController::class, 'showMutasiBahanBaku'])->name('mutasi-bahan-baku.show');

    Route::get('/persetujuan-workorder', function () { 
        return view('owner.persetujuan-workorder'); 
    })->name('persetujuan-workorder');

    Route::resource('users', UserManagementController::class)->except(['show']);

    // Rute Perintah Produksi untuk Approval Owner
    Route::get('perintah-produksi', [PerintahProduksiOwnerController::class, 'index'])->name('perintah-produksi.index');
    Route::post('perintah-produksi/{perintahProduksi}/approve', [PerintahProduksiOwnerController::class, 'approve'])->name('perintah-produksi.approve');
    Route::post('perintah-produksi/{perintahProduksi}/reject', [PerintahProduksiOwnerController::class, 'reject'])->name('perintah-produksi.reject');

    // Rute Pantau Progres Produksi Owner
    Route::get('pantau-progres', [PerintahProduksiOwnerController::class, 'pantauProgres'])->name('pantau-progres.index');
    Route::get('pantau-progres/{perintahProduksi}', [PerintahProduksiOwnerController::class, 'showProgres'])->name('pantau-progres.show');

    // Rute Riwayat Transaksi Penjualan Owner
    Route::get('riwayat-penjualan', [\App\Http\Controllers\Owner\RiwayatPenjualanOwnerController::class, 'index'])->name('riwayat-penjualan.index');
    Route::get('riwayat-penjualan/{penjualan}', [\App\Http\Controllers\Owner\RiwayatPenjualanOwnerController::class, 'show'])->name('riwayat-penjualan.show');
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardAdminController::class, 'index'])->name('dashboard');

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
    Route::resource('standard-baseline-produksi', StandardBaselineProduksiController::class)->except(['create', 'edit']);

    // Rute Pergerakan Stok Bahan Baku Bulk
    Route::resource('pergerakan-stok', PergerakanStokController::class)->except(['edit', 'update']);

    // Rute Perintah Produksi
    Route::resource('perintah-produksi', PerintahProduksiController::class);
    Route::post('perintah-produksi/{perintahProduksi}/selesai', [PerintahProduksiController::class, 'selesai'])->name('perintah-produksi.selesai');
    Route::get('perintah-produksi/{perintahProduksi}/cetak-pdf', [PerintahProduksiController::class, 'cetakPdf'])->name('perintah-produksi.cetak-pdf');

    // Rute Penerimaan Hasil Produksi
    Route::post('penerimaan-hasil-produksi', [PenerimaanHasilProduksiController::class, 'store'])->name('penerimaan-hasil-produksi.store');
    Route::get('penerimaan-hasil-produksi/{detail}/history', [PenerimaanHasilProduksiController::class, 'history'])->name('penerimaan-hasil-produksi.history');
    Route::post('penerimaan-hasil-produksi/{penerimaan}/reversal', [PenerimaanHasilProduksiController::class, 'reversal'])->name('penerimaan-hasil-produksi.reversal');
    Route::get('penerimaan-hasil-produksi/{detail}/available-karyawan', [PenerimaanHasilProduksiController::class, 'availableKaryawan'])->name('penerimaan-hasil-produksi.available-karyawan');

    // Rute Transaksi Penjualan
    Route::resource('penjualan', PenjualanController::class);
});

Route::middleware(['auth', 'role:potong,jahit,finishing'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', [DashboardProduksiController::class, 'index'])->name('dashboard');
    Route::get('/perintah-produksi', [PerintahProduksiKaryawanController::class, 'index'])->name('perintah-produksi.index');
    Route::get('/perintah-produksi/{perintahProduksi}', [PerintahProduksiKaryawanController::class, 'show'])->name('perintah-produksi.show');

    Route::get('/input-hasil', [DashboardProduksiController::class, 'index'])->name('input-hasil.index');
    
    Route::post('/input-hasil', [InputHasilPekerjaanController::class, 'store'])->name('input-hasil.store');
    Route::post('/produk-cacat', [ProdukCacatController::class, 'store'])->name('produk-cacat.store');

    Route::get('/ajuan-saya', [AjuanPengambilanProduksiController::class, 'index'])->name('ajuan-pengambilan.index');
    Route::get('/ajuan-masuk', [AjuanPengambilanProduksiController::class, 'masuk'])->name('ajuan-pengambilan.masuk');
    Route::get('/ajuan-pengambilan', [AjuanPengambilanProduksiController::class, 'redirectLegacy']);
    Route::post('/ajuan-pengambilan', [AjuanPengambilanProduksiController::class, 'store'])->name('ajuan-pengambilan.store');
    Route::post('/ajuan-pengambilan/{ajuan}/approve', [AjuanPengambilanProduksiController::class, 'approve'])->name('ajuan-pengambilan.approve');
    Route::post('/ajuan-pengambilan/{ajuan}/reject', [AjuanPengambilanProduksiController::class, 'reject'])->name('ajuan-pengambilan.reject');

    Route::post('/potong/{detail}/input-hasil', [PotongController::class, 'inputHasil'])->name('potong.input-hasil');
});
