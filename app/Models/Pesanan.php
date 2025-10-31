<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// 🔽 HAPUS 'use Illuminate\Database\Eloquent\Casts\Attribute;' 🔽
// (JIKA ADA)

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'id_pengguna',
        'customer_name',
        'nomor_meja',
        'tanggal_pesan',
        'total_harga',
        'status_pesanan',
        'status_pembayaran',
        'catatan',
        'tanggal_pembayaran',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function items()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }
}
