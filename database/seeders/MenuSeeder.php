<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::insert([
            [
                'nama_menu' => 'Kopi Arabika',
                'deskripsi' => 'Kopi murni dari biji arabika pilihan.',
                'harga' => 25000,
                'stok' => 30,
                'kategori' => 'Kopi',
                'gambar' => 'arabika.jpg',
            ],
            [
                'nama_menu' => 'Kentang Goreng',
                'deskripsi' => 'Kentang goreng renyah dengan saus sambal dan mayo.',
                'harga' => 15000,
                'stok' => 20,
                'kategori' => 'Snack',
                'gambar' => 'kentang.jpg',
            ],
            [
                'nama_menu' => 'Ricebowl Ayam Teriyaki',
                'deskripsi' => 'Nasi hangat dengan ayam teriyaki dan sayur segar.',
                'harga' => 25000,
                'stok' => 15,
                'kategori' => 'Makanan',
                'gambar' => 'ricebowl.jpg',
            ],
        ]);
    }
}