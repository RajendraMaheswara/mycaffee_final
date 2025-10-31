<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DetailPesanan;
use App\Models\Pesanan; // TAMBAHKAN INI
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class DetailPesananController extends Controller
{
    /**
     * Display details for a specific pesanan.
     */
    public function showByPesanan($id_pesanan)
    {
        $details = DetailPesanan::with('menu')
            ->where('id_pesanan', $id_pesanan)
            ->get();

        return response()->json(['data' => $details], Response::HTTP_OK);
    }

    /**
     * Update the specified detail pesanan.
     */
    public function update(Request $request, $id)
    {
        $detail = DetailPesanan::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail pesanan not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $menu = Menu::find($detail->id_menu);
            
            // Kembalikan stok sebelumnya
            if (isset($menu->stok)) {
                $menu->stok += $detail->jumlah;
                $menu->save();
            }

            // Kurangi stok dengan jumlah baru
            if (isset($menu->stok)) {
                if ($menu->stok < $validated['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stok untuk menu {$menu->nama_menu} tidak cukup"
                    ], 422);
                }
                $menu->stok -= $validated['jumlah'];
                $menu->save();
            }

            $detail->jumlah = $validated['jumlah'];
            $detail->save();

            // Update total harga di pesanan
            $pesanan = $detail->pesanan;
            $total = DetailPesanan::where('id_pesanan', $pesanan->id)
                ->sum(DB::raw('jumlah * harga_satuan'));
            
            $pesanan->total_harga = $total;
            $pesanan->save();

            DB::commit();

            return response()->json([
                'data' => $detail->load('menu')
            ], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified detail pesanan.
     */
    public function destroy($id)
    {
        $detail = DetailPesanan::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail pesanan not found'], Response::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok menu
            $menu = Menu::find($detail->id_menu);
            if (isset($menu->stok)) {
                $menu->stok += $detail->jumlah;
                $menu->save();
            }

            $id_pesanan = $detail->id_pesanan;
            $detail->delete();

            // Update total harga di pesanan
            $pesanan = Pesanan::find($id_pesanan);
            $total = DetailPesanan::where('id_pesanan', $id_pesanan)
                ->sum(DB::raw('jumlah * harga_satuan'));
            
            $pesanan->total_harga = $total;
            $pesanan->save();

            DB::commit();

            return response()->json(['message' => 'Detail pesanan deleted'], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}