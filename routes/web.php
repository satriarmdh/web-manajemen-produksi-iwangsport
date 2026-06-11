<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner\UserManagementController;
use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\EstimasiProduksiController;

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
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { 
        return view('admin.dashboard'); 
    })->name('dashboard');    

    // Rute pengelolaan Bahan Baku
    Route::resource('bahan-baku', BahanBakuController::class)->except(['create', 'show', 'edit']);

    // Rute pengelolaan Produk
    Route::resource('produk', ProdukController::class)->except(['create', 'show', 'edit']);

    // Rute pengelolaan Supplier
    Route::resource('supplier', SupplierController::class)->except(['create', 'show', 'edit']);

    // Rute pengelolaan Estimasi Produksi (Standard Baseline)
    Route::resource('estimasi-produksi', EstimasiProduksiController::class)->except(['create', 'show', 'edit']);
});

Route::middleware(['auth'])->group(function () {

    // RUTE AWAL Produksi HANYA UNTUK TESTING
    Route::get('/produksi/potong', function () { return 'Produksi Potong'; })->name('produksi.potong');
    Route::get('/produksi/jahit', function () { return 'Produksi Jahit'; })->name('produksi.jahit');
    Route::get('/produksi/finishing', function () { return 'Produksi Finishing'; })->name('produksi.finishing');
});
