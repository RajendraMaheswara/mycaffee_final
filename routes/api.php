<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KasirController;
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
    // Route dashboard kasir (dari file ori kamu)
    Route::get('/dashboard_kasir', fn() => response()->json([
        'message' => 'Selamat datang di Dashboard Kasir',
        'info' => 'Hanya kasir yang bisa melihat endpoint ini'
    ]));

    // --- ROUTE KASIR CONTROLLER DIMASUKKAN DI SINI ---

    // ▼▼▼ TAMBAHKAN ROUTE BARU INI ▼▼▼
    // [GET] /api/kasir/menu (Untuk mengisi dropdown di view 'tambah_item')
    Route::get('/menu', [KasirController::class, 'getMenu']);

    // [GET] /api/kasir/pesanan
    // (Job Desk: Mengambil semua pesanan aktif)
    Route::get('/pesanan', [KasirController::class, 'index']);

    // [GET] /api/kasir/pesanan/{id}
    // (Job Desk: Melihat Detail Pesanan)
    Route::get('/pesanan/{id}', [KasirController::class, 'show']);

    // [POST] /api/kasir/pesanan/{id}/add-item
    // (Job Desk: Menambah Pesanan yang sudah dipesan)
    Route::post('/pesanan/{id}/add-item', [KasirController::class, 'addItem']);

    // [PATCH] /api/kasir/pesanan/{id}/status
    // (Job Desk: Mengganti status pesanan & pembayaran)
    Route::patch('/pesanan/{id}/status', [KasirController::class, 'updateStatus']);


});
