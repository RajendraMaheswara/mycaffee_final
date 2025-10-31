<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// ▼▼▼ TAMBAHKAN DUA 'use' INI ▼▼▼
use App\Models\Pesanan;
use App\Models\Menu;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';

    protected $fillable = [
        'id_pesanan',
        'id_menu',
        'jumlah',
        'harga_satuan',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}
