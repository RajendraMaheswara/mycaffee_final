<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        Pesanan::insert([
            [
                'id_pengguna' => null,
                'nomor_meja' => 5,
                'total_harga' => 40000,
                'status_pesanan' => 'diproses',
                'status_pembayaran' => 'belum_dibayar',
                'catatan' => 'Tanpa gula untuk kopi.',
                'tanggal_pesan' => now(),
                'tanggal_pembayaran' => null,
            ],
            [
                'id_pengguna' => null,
                'nomor_meja' => 10,
                'total_harga' => 25000,
                'status_pesanan' => 'diantar',
                'status_pembayaran' => 'belum_dibayar',
                'catatan' => null,
                'tanggal_pesan' => now(),
                'tanggal_pembayaran' => null,
            ],
        ]);
    }
}