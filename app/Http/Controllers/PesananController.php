<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Display a listing of pesanan.
     */
    public function index()
    {
        $pesanan = Pesanan::with(['pengguna', 'detail.menu'])->orderBy('tanggal_pesan', 'desc')->get();

        return response()->json(['data' => $pesanan], Response::HTTP_OK);
    }

    /**
     * Store a newly created pesanan along with detail items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pengguna' => 'nullable|exists:pengguna,id',
            'nomor_meja' => 'required|integer',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_menu' => 'required|exists:menu,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pesanan = Pesanan::create([
                'id_pengguna' => $validated['id_pengguna'] ?? null,
                'nomor_meja' => $validated['nomor_meja'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $total = 0;
            foreach ($validated['items'] as $it) {
                $menu = Menu::findOrFail($it['id_menu']);
                $harga = $menu->harga;
                $jumlah = $it['jumlah'];

                $detail = DetailPesanan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_menu' => $menu->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                ]);

                $total += bcmul((string)$harga, (string)$jumlah, 2);

                // optionally decrease stock if present
                if (isset($menu->stok)) {
                    $menu->stok = max(0, $menu->stok - $jumlah);
                    $menu->save();
                }
            }

            $pesanan->total_harga = $total;
            $pesanan->save();

            DB::commit();

            return response()->json(['data' => $pesanan->load('detail.menu')], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified pesanan.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['pengguna', 'detail.menu'])->find($id);
        if (! $pesanan) {
            return response()->json(['message' => 'Pesanan not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $pesanan], Response::HTTP_OK);
    }

    /**
     * Update pesanan (only certain fields and items).
     */
    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::with('detail')->find($id);
        if (! $pesanan) {
            return response()->json(['message' => 'Pesanan not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'nomor_meja' => 'sometimes|integer',
            'catatan' => 'nullable|string',
            'status_pesanan' => 'nullable|in:diproses,diantar',
            'status_pembayaran' => 'nullable|in:belum_dibayar,lunas',
            'items' => 'nullable|array',
            'items.*.id_menu' => 'required_with:items|exists:menu,id',
            'items.*.jumlah' => 'required_with:items|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pesanan->fill(array_intersect_key($validated, array_flip(['nomor_meja','catatan','status_pesanan','status_pembayaran'])));
            $pesanan->save();

            if (! empty($validated['items'])) {
                // Remove existing detail and recreate — simplifies handling
                $pesanan->detail()->delete();
                $total = 0;
                foreach ($validated['items'] as $it) {
                    $menu = Menu::findOrFail($it['id_menu']);
                    $harga = $menu->harga;
                    $jumlah = $it['jumlah'];

                    DetailPesanan::create([
                        'id_pesanan' => $pesanan->id,
                        'id_menu' => $menu->id,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $harga,
                    ]);

                    $total += bcmul((string)$harga, (string)$jumlah, 2);
                }
                $pesanan->total_harga = $total;
                $pesanan->save();
            }

            DB::commit();
            return response()->json(['data' => $pesanan->load('detail.menu')], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified pesanan.
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::find($id);
        if (! $pesanan) {
            return response()->json(['message' => 'Pesanan not found'], Response::HTTP_NOT_FOUND);
        }

        $pesanan->delete();
        return response()->json(['message' => 'Pesanan deleted'], Response::HTTP_OK);
    }

    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'nomor_meja' => 'required|integer',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_menu' => 'required|exists:menu,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pesanan = Pesanan::create([
                'id_pengguna' => null, // Public order, no user account
                'nomor_meja' => $validated['nomor_meja'],
                'catatan' => $validated['catatan'] ?? null,
                'status_pesanan' => 'diproses',
                'status_pembayaran' => 'belum_dibayar',
            ]);

            $total = 0;
            foreach ($validated['items'] as $it) {
                $menu = Menu::findOrFail($it['id_menu']);
                $harga = $menu->harga;
                $jumlah = $it['jumlah'];

                // Check stock
                if (isset($menu->stok) && $menu->stok < $jumlah) {
                    throw new \Exception("Stok untuk menu {$menu->nama_menu} tidak cukup");
                }

                $detail = DetailPesanan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_menu' => $menu->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $harga,
                ]);

                $total += $harga * $jumlah;

                // Decrease stock
                if (isset($menu->stok)) {
                    $menu->stok = max(0, $menu->stok - $jumlah);
                    $menu->save();
                }
            }

            $pesanan->total_harga = $total;
            $pesanan->save();

            DB::commit();

            return response()->json([
                'message' => 'Pesanan berhasil dibuat',
                'data' => [
                    'id_pesanan' => $pesanan->id,
                    'nomor_meja' => $pesanan->nomor_meja,
                    'total_harga' => $pesanan->total_harga,
                    'status_pesanan' => $pesanan->status_pesanan
                ]
            ], Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

