<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetailPesanan;

class DetailPesananSeeder extends Seeder
{
    public function run(): void
    {
        DetailPesanan::insert([
            ['id_pesanan' => 1, 'id_menu' => 1, 'jumlah' => 1, 'harga_satuan' => 25000],
            ['id_pesanan' => 1, 'id_menu' => 2, 'jumlah' => 1, 'harga_satuan' => 15000],
        ]);
    }
}