<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserOrderController;
use Illuminate\Support\Facades\Route;

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