<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Menu;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    /**
     * Show the order form for QR code users
     */
    public function create(Request $request)
    {
        $nomor_meja = $request->query('table', 1);
        $menus = Menu::where('stok', '>', 0)->get();

        return view('user.order', compact('nomor_meja', 'menus'));
    }

    /**
     * Process the order from QR code
     */
    public function store(Request $request)
    {
        // Validasi manual karena data items dikirim sebagai JSON string
        $request->validate([
            'nomor_meja' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        $items = json_decode($request->items, true);
        
        if (!is_array($items) || count($items) === 0) {
            return back()->withErrors(['error' => 'Silakan pilih minimal satu menu']);
        }

        // Validasi setiap item
        foreach ($items as $item) {
            if (empty($item['id_menu']) || empty($item['jumlah']) || $item['jumlah'] < 1) {
                return back()->withErrors(['error' => 'Data menu tidak valid']);
            }
            
            $menu = Menu::find($item['id_menu']);
            if (!$menu) {
                return back()->withErrors(['error' => 'Menu tidak ditemukan']);
            }
            
            if ($menu->stok < $item['jumlah']) {
                return back()->withErrors(['error' => "Stok {$menu->nama_menu} tidak cukup"]);
            }
        }

        DB::beginTransaction();
        try {
            $pesanan = Pesanan::create([
                'id_pengguna' => null,
                'nomor_meja' => $request->nomor_meja,
                'catatan' => $request->catatan,
                'status_pesanan' => 'diproses',
                'status_pembayaran' => 'belum_dibayar',
                'tanggal_pesan' => now(),
            ]);

            $total = 0;
            foreach ($items as $item) {
                $menu = Menu::find($item['id_menu']);
                $harga = $menu->harga;
                $jumlah = $item['jumlah'];

                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_menu' => $menu->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                ]);

                $total += $harga * $jumlah;

                // Update stok
                $menu->stok -= $jumlah;
                $menu->save();
            }

            $pesanan->total_harga = $total;
            $pesanan->save();

            DB::commit();

            return redirect()->route('user.order.confirm', ['id' => $pesanan->id]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show order confirmation
     */
    public function confirm($id)
    {
        $pesanan = Pesanan::with('detail.menu')->find($id);
        if (!$pesanan) {
            abort(404);
        }

        return view('user.confirmation', compact('pesanan'));
    }
}