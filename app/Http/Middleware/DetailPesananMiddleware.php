<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Menu;
use App\Models\DetailPesanan;
/**
 * Middleware to validate detail pesanan related requests.
 * - If route has detail id, load the DetailPesanan model and attach to request
 * - If request has items payload, validate each item: menu exists and stok sufficient
 */
class DetailPesananMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If a specific detail id is present, load it
        $detailId = $request->route('id') ?? $request->route('detail');
        if ($detailId) {
            $detail = DetailPesanan::with('menu')->find($detailId);
            if (! $detail) {
                return response()->json(['message' => 'Detail Pesanan not found'], 404);
            }

            $request->attributes->set('detail_pesanan', $detail);
        }

        // If items payload exists (store/update), validate menus and stok
        $items = $request->input('items');
        if (is_array($items)) {
            foreach ($items as $idx => $it) {
                if (empty($it['id_menu']) || empty($it['jumlah'])) {
                    return response()->json(['message' => "Invalid item at index {$idx}"], 422);
                }

                $menu = Menu::find($it['id_menu']);
                if (! $menu) {
                    return response()->json(['message' => "Menu with id {$it['id_menu']} not found"], 404);
                }

                // Check stock if column exists
                if (isset($menu->stok) && $menu->stok < $it['jumlah']) {
                    return response()->json(['message' => "Stok untuk menu {$menu->nama_menu} tidak cukup"], 422);
                }
            }
        }

        return $next($request);
    }
}
