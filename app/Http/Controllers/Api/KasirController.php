<?php

namespace App\Http\Controllers\Api;

// Import Model
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Menu; // <-- PASTIKAN IMPORT INI ADA

// Import Resource
use App\Http\Resources\PesananResource;

// Import bawaan Laravel
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    /**
     * index()
     */
    public function index()
    {
        $pesanan = Pesanan::where('status_pesanan', '!=', 'selesai')
                            ->latest()
                            ->paginate(10);
        return new PesananResource(true, 'List Data Pesanan Aktif', $pesanan);
    }

    /**
     * Mengambil semua data menu untuk dropdown
     */
    public function getMenu()
    {
        // ▼▼▼ PERBAIKAN: Filter diubah dari 'is_ready' menjadi 'stok > 0' ▼▼▼
        $menu = Menu::where('stok', '>', 0)->get();

        return response()->json([
            'success' => true,
            'data' => $menu
        ]);
    }

    /**
     * Menampilkan detail satu pesanan
     */
    public function show($id)
    {
        $pesanan = Pesanan::with('items.menu')->find($id);

        if (!$pesanan) {
            return new PesananResource(false, 'Pesanan Tidak Ditemukan!', null);
        }

        return new PesananResource(true, 'Detail Data Pesanan!', $pesanan);
    }

    /**
     * Menambah item ke pesanan yang sudah ada
     */
    public function addItem(Request $request, $id)
    {
        $pesanan = Pesanan::find($id);

        if (!$pesanan) {
            return new PesananResource(false, 'Pesanan Tidak Ditemukan!', null);
        }

        if ($pesanan->status_pembayaran == 'lunas' || $pesanan->status_pesanan == 'selesai') {
             return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menambah item, pesanan sudah selesai atau lunas.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'id_menu'  => 'required|exists:menus,id',
            'jumlah'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $menuItem = Menu::find($request->id_menu);

        // ▼▼▼ LOGIKA STOK (Opsional tapi disarankan) ▼▼▼
        // if ($menuItem->stok < $request->jumlah) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Stok tidak mencukupi. Sisa stok: ' . $menuItem->stok,
        //     ], 422);
        // }
        // $menuItem->stok -= $request->jumlah;
        // $menuItem->save();
        // ▲▲▲ (Kamu bisa tambahkan logika pengurangan stok di atas jika perlu) ▲▲▲

        $harga_satuan = $menuItem->harga;

        $detail = DetailPesanan::create([
            'id_pesanan'   => $pesanan->id,
            'id_menu'      => $request->id_menu,
            'jumlah'       => $request->jumlah,
            'harga_satuan' => $harga_satuan,
        ]);

        $pesanan->total_harga += ($harga_satuan * $request->jumlah);
        $pesanan->save();

        $pesanan->load('items.menu');

        return new PesananResource(true, 'Item Berhasil Ditambahkan ke Pesanan!', $pesanan);
    }


    /**
     * updateStatus()
     */
    public function updateStatus(Request $request, $id)
    {
        $pesanan = Pesanan::find($id);

        if (!$pesanan) {
            return new PesananResource(false, 'Pesanan Tidak Ditemukan!', null);
        }

        $validator = Validator::make($request->all(), [
            'status_pesanan'    => 'sometimes|in:diproses,diantar,selesai',
            'status_pembayaran' => 'sometimes|in:belum dibayar,lunas',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('status_pesanan')) {
            $pesanan->status_pesanan = $request->status_pesanan;
            if ($request->status_pesanan == 'selesai' && is_null($pesanan->id_pengguna)) {
                $pesanan->id_pengguna = Auth::id();
            }
        }

        if ($request->has('status_pembayaran')) {
            $pesanan->status_pembayaran = $request->status_pembayaran;
        }

        $pesanan->save();

        return new PesananResource(true, 'Status Pesanan Berhasil Diupdate!', $pesanan);
    }
}
