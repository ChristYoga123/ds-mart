<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Kasir\CashierController;
use App\Http\Controllers\Kasir\TransaksiController;

Route::redirect('/', 'login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('kasir')->name('kasir.')->group(function()
    {
        Route::get('/', [CashierController::class, 'index'])->name('index');
        Route::post('/bayar', [CashierController::class, 'bayar'])->name('bayar');
        
        // Route untuk transaksi
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::get('/transaksi/data', [TransaksiController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/widget', [TransaksiController::class, 'widget'])->name('transaksi.widget');
        Route::get('/transaksi/detail/{kodeTransaksi}', [TransaksiController::class, 'detail'])->name('transaksi.detail');
    });
});
