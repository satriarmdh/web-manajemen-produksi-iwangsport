<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () { return 'Admin Dashboard'; })->name('admin.dashboard');
    Route::get('/owner/dashboard', function () { return 'Owner Dashboard'; })->name('owner.dashboard');
    Route::get('/produksi/potong', function () { return 'Produksi Potong'; })->name('produksi.potong');
    Route::get('/produksi/jahit', function () { return 'Produksi Jahit'; })->name('produksi.jahit');
    Route::get('/produksi/finishing', function () { return 'Produksi Finishing'; })->name('produksi.finishing');
});
