<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Total Statistics
        $totalMenu = Menu::count();
        $totalPesanan = Pesanan::count();
        $totalPendapatan = Pesanan::where('status_pembayaran', 'sudah dibayar')->sum('total_harga');
        $totalPengguna = Pengguna::count();

        // Recent Orders
        $recentPesanan = Pesanan::with('kasir')
            ->latest()
            ->take(5)
            ->get();

        // Low Stock Alert
        $lowStockMenus = Menu::where('stok', '<', 5)
            ->orderBy('stok')
            ->take(5)
            ->get();

        // Popular Menu (berdasarkan detail pesanan)
        $popularMenus = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->groupBy('menu.id_menu', 'menu.nama_menu')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // Monthly Revenue Chart Data
        $monthlyRevenue = Pesanan::where('status_pembayaran', 'sudah dibayar')
            ->whereYear('tanggal_pesan', date('Y'))
            ->select(
                DB::raw('MONTH(tanggal_pesan) as month'),
                DB::raw('SUM(total_harga) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Order Status Statistics
        $orderStatusStats = Pesanan::select('status_pesanan', DB::raw('COUNT(*) as count'))
            ->groupBy('status_pesanan')
            ->get();

        $paymentStatusStats = Pesanan::select('status_pembayaran', DB::raw('COUNT(*) as count'))
            ->groupBy('status_pembayaran')
            ->get();

        return view('admin.dashboard.index', compact(
            'totalMenu',
            'totalPesanan',
            'totalPendapatan',
            'totalPengguna',
            'recentPesanan',
            'lowStockMenus',
            'popularMenus',
            'monthlyRevenue',
            'orderStatusStats',
            'paymentStatusStats'
        ));
    }
}