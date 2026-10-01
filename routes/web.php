<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

// Pintu masuk: tamu dan customer ke etalase, admin ke dashboard.
Route::get('/', function () {
    if (auth()->check() && auth()->user()->isAdmin()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('beranda');
})->name('home');

// Etalase terbuka untuk semua orang, termasuk tamu.
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

// Tamu: login dan daftar
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.attempt');
});

// Wajib login (admin maupun customer)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman customer
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::get('/pesanan-saya', [OrderController::class, 'riwayat'])->name('order.riwayat');
    Route::post('/pesanan-saya/{kode}/bukti-pembayaran', [OrderController::class, 'unggahBuktiPembayaran'])->name('order.bukti-pembayaran');

    // Pemesanan
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::post('/order/hitung-ongkir', [OrderController::class, 'hitungOngkir'])->middleware('throttle:15,1')->name('order.delivery-quote');
    Route::post('/order/keranjang', [OrderController::class, 'keranjang'])->name('order.keranjang');
    Route::get('/order/sukses', [OrderController::class, 'sukses'])->name('order.sukses');

    // Menu admin
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::patch('/pembayaran/{pesanan}', [PembayaranController::class, 'update'])->name('pembayaran.update');
        Route::get('/pembayaran/{pesanan}/bukti', [PembayaranController::class, 'bukti'])->name('pembayaran.bukti');
        Route::get('/search', [SearchController::class, 'index'])->name('search.index');
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/stok/riwayat', [StokController::class, 'riwayat'])->name('stok.riwayat');
        Route::post('/stok/tambah', [StokController::class, 'tambah'])->name('stok.tambah');
        Route::get('/drivers', [DriverController::class, 'index'])->name('driver.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('driver.store');
        Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('driver.update');
        Route::patch('/drivers/{driver}/status', [DriverController::class, 'toggleStatus'])->name('driver.status');
        Route::post('/drivers/{driver}/assign', [DriverController::class, 'tugaskan'])->name('driver.assign');
        Route::patch('/drivers/{driver}/pesanan/{pesanan}/selesai', [DriverController::class, 'selesaikanPesanan'])->name('driver.pesanan.selesai');
        Route::post('/pesanan/{pesanan}/driver', [DriverController::class, 'tugaskanPesanan'])->name('pesanan.driver');
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::put('pelanggan/{user}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::patch('pelanggan/{user}/status', [PelangganController::class, 'toggleStatus'])->name('pelanggan.status');

        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::put('pengaturan/password', [PengaturanController::class, 'password'])->name('pengaturan.password');

        Route::resource('kategori', KategoriController::class)->except('show');
        Route::resource('produk', ProdukController::class)->except('show');
        Route::resource('pesanan', PesananController::class)->except('show')
            ->parameters(['pesanan' => 'pesanan']);
        Route::patch('pesanan/{pesanan}/status', [PesananController::class, 'ubahStatus'])->name('pesanan.status');
        Route::patch('pesanan/{pesanan}/pembayaran', [PesananController::class, 'ubahPembayaran'])->name('pesanan.pembayaran');
        Route::patch('pesanan/{pesanan}/ongkir', [PesananController::class, 'ubahOngkir'])->name('pesanan.ongkir');

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('penjualan');
            Route::get('/preview', [LaporanController::class, 'preview'])->name('preview');
            Route::get('/download', [LaporanController::class, 'download'])->name('download');
        });
    });
});