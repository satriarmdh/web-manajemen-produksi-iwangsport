<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Owner\UserManagementController;

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
    Route::resource('bahan-baku', \App\Http\Controllers\Admin\BahanBakuController::class)->except(['create', 'show', 'edit']);

    // Rute pengelolaan Produk
    Route::resource('produk', \App\Http\Controllers\Admin\ProdukController::class)->except(['create', 'show', 'edit']);
});

Route::middleware(['auth'])->group(function () {
    // Route::get('/admin/dashboard', function () { return 'Admin Dashboard'; })->name('admin.dashboard');


    Route::get('/produksi/potong', function () { return 'Produksi Potong'; })->name('produksi.potong');
    Route::get('/produksi/jahit', function () { return 'Produksi Jahit'; })->name('produksi.jahit');
    Route::get('/produksi/finishing', function () { return 'Produksi Finishing'; })->name('produksi.finishing');
});
