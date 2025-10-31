<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'nama_menu',
        'deskripsi',
        'harga',
        'stok',
        'kategori',
        'gambar',
    ];

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu');
    }
}
