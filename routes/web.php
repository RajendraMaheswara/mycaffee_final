<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Pengguna\UserOrderController;
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

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->name('admin.menu.')
    ->group(function () {
        Route::get('/menu', [MenuController::class, 'index'])->name('index');
        Route::get('/menu/tambah', [MenuController::class, 'create'])->name('create');
        Route::post('/menu', [MenuController::class, 'store'])->name('store');
        Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name('edit');
        Route::put('/menu/{id}', [MenuController::class, 'update'])->name('update');
        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('destroy');
    });