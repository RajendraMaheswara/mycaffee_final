<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MenuController;

// Route::get('/', function () {
//    return redirect('/login');
// });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard_admin', function () {
        return view('admin.dashboard_admin');
    })->name('admin.dashboard');
});

// KASIR
Route::middleware(['auth:sanctum', 'role:kasir'])->group(function () {
    Route::get('/kasir/dashboard_kasir', function () {
        return view('kasir.dashboard_kasir');
    })->name('kasir.dashboard');
});

// User Order (Public Routes)
Route::get('/', [UserOrderController::class, 'create'])->name('user.order.create');
Route::post('/', [UserOrderController::class, 'store'])->name('user.order.store');
Route::get('/confirmation/{id}', [UserOrderController::class, 'confirm'])->name('user.order.confirm');

Route::prefix('admin/menu')->name('admin.menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/tambah', [MenuController::class, 'create'])->name('create');
    Route::post('/simpan', [MenuController::class, 'store'])->name('store');
    Route::get('/edit/{id_menu}', [MenuController::class, 'edit'])->name('edit');
    Route::post('/update/{id_menu}', [MenuController::class, 'update'])->name('update');
    Route::get('/hapus/{id_menu}', [MenuController::class, 'destroy'])->name('destroy');
});

