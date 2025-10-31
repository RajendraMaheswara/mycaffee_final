<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Pengguna\PesananController;
use App\Http\Controllers\Pengguna\DetailPesananController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

// Public routes untuk user order dari QR code
Route::post('/pesanan/public', [PesananController::class, 'storePublic']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Pesanan routes
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::put('/pesanan/{id}', [PesananController::class, 'update']);
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy']);

    // Detail Pesanan routes
    Route::get('/pesanan/{id_pesanan}/detail', [DetailPesananController::class, 'showByPesanan']);
    Route::put('/detail-pesanan/{id}', [DetailPesananController::class, 'update']);
    Route::delete('/detail-pesanan/{id}', [DetailPesananController::class, 'destroy']);
});

// 🔹 Route khusus admin
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard_admin', fn() => response()->json([
        'message' => 'Selamat datang di Dashboard Admin',
        'info' => 'Hanya admin yang bisa melihat endpoint ini'
    ]));
});

// 🔹 Route khusus kasir
Route::prefix('kasir')->middleware(['auth:sanctum', 'role:kasir'])->group(function () {
    Route::get('/dashboard_kasir', fn() => response()->json([
        'message' => 'Selamat datang di Dashboard Kasir',
        'info' => 'Hanya kasir yang bisa melihat endpoint ini'
    ]));
});