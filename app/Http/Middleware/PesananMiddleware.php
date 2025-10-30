<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Pesanan;

/**
 * Middleware to ensure pesanan exists and the authenticated user
 * has permission to access it (owner or roles admin/kasir).
 */

class PesananMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('id') ?? $request->route('pesanan');

        if ($id) {
            $pesanan = Pesanan::with('pengguna')->find($id);
            if (! $pesanan) {
                return response()->json(['message' => 'Pesanan not found'], 404);
            }

            // attach model to request for controllers
            $request->attributes->set('pesanan', $pesanan);

            // if user is authenticated, check role or ownership
            $user = $request->user();
            if ($user) {
                $peran = $user->peran ?? null;
                $isOwner = ($pesanan->id_pengguna && $user->id == $pesanan->id_pengguna);

                if (! in_array($peran, ['admin', 'kasir']) && ! $isOwner) {
                    return response()->json(['message' => 'Akses ditolak'], 403);
                }
            }
        }

        return $next($request);
    }
}
