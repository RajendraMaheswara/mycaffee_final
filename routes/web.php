<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pengguna\UserOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuController;

use App\Http\Controllers\Admin\PenggunaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route untuk halaman yang bisa dilihat di browser (views).
|
*/

// Mengarahkan halaman utama langsung ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// User Order (Public Routes)
Route::get('/', [UserOrderController::class, 'create'])->name('user.order.create');
Route::post('/', [UserOrderController::class, 'store'])->name('user.order.store');
Route::get('/confirmation/{id}', [UserOrderController::class, 'confirm'])->name('user.order.confirm');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard_admin', function () {
        return view('admin.dashboard_admin');
    })->name('admin.dashboard');
});

// KASIR
Route::middleware(['auth', 'role:kasir'])->group(function () {
// (Grup yang duplikat sudah digabung ke sini)
Route::middleware(['auth:sanctum', 'role:kasir'])->group(function () {

    // Route untuk dashboard kasir
    Route::get('/kasir/dashboard_kasir', function () {
        return view('kasir.dashboard_kasir');
    })->name('kasir.dashboard');

    // Route untuk menampilkan HALAMAN detail pesanan
    Route::get('/kasir/pesanan/{id}', function ($id) {
        // Kita kirim 'id' ke view, agar JavaScript di view itu tahu
        // pesanan mana yang harus di-fetch dari API
        return view('kasir.detail_pesanan', ['id_pesanan' => $id]);
    })->name('kasir.pesanan.detail');
});

// BLOK KASIR KEDUA YANG DUPLIKAT SUDAH DIHAPUS DARI SINI

// ▼▼▼ TAMBAHKAN ROUTE BARU INI ▼▼▼
    // Route untuk HALAMAN BARU "Tambah Item"
    Route::get('/kasir/pesanan/{id}/tambah-item', function ($id) {
        // Kirim ID pesanan ke view
        return view('kasir.tambah_item', ['id_pesanan' => $id]);
    })->name('kasir.pesanan.tambah_item'); // Beri nama agar bisa dipanggil
});

Route::prefix('admin/menu')->name('admin.menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/tambah', [MenuController::class, 'create'])->name('create');
    Route::post('/simpan', [MenuController::class, 'store'])->name('store');
    Route::get('/edit/{id_menu}', [MenuController::class, 'edit'])->name('edit');
    Route::post('/update/{id_menu}', [MenuController::class, 'update'])->name('update');
    Route::get('/hapus/{id_menu}', [MenuController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/pengguna')->name('admin.pengguna.')->group(function () {
    Route::get('/', [PenggunaController::class, 'index'])->name('index');
    Route::get('/tambah', [PenggunaController::class, 'create'])->name('create');
    Route::post('/simpan', [PenggunaController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [PenggunaController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [PenggunaController::class, 'update'])->name('update');
    Route::get('/hapus/{id}', [PenggunaController::class, 'destroy'])->name('destroy');
});
