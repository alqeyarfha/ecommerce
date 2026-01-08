<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $stats = [
            'total_revenue' => Order::whereIn('status', ['processing', 'completed'])
                                    ->orWhere(function($q) {
                                        $q->where('payment_status', 'paid')
                                          ->where('status', 'pending');
                                    })
                                    ->sum('total_amount'),

            'total_orders' => Order::count(),
            'pending_orders' => Order::where('payment_status', 'paid')
                                     ->whereIn('status', ['pending', 'processing'])
                                     ->count(),
            'total_products' => Product::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'low_stock' => Product::where('stock', '<=', 10)->count(),
        ];

        // 2. Pesanan Terbaru
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 3. PRODUK TERLARIS - FIX 100% (hanya kolom yang pasti ada)
        $topProducts = DB::table('products as p')
            ->leftJoin('order_items as oi', 'p.id', '=', 'oi.product_id')
            ->leftJoin('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('o.payment_status', 'paid')
            ->select([
                'p.id',
                'p.name',
                'p.slug',
                'p.price',
                DB::raw('COALESCE(SUM(oi.quantity), 0) as sold')
            ])
            ->groupBy('p.id', 'p.name', 'p.slug', 'p.price')
            ->havingRaw('sold > 0')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // 4. Grafik Pendapatan 7 Hari
        $revenueChart = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'revenueChart'));
    }
}
