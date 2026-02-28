<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Statut;
use App\Models\Client;
use App\Models\Avis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboradController extends Controller
{
    public function index()
    {
        /* ────────── 1. Produits ────────── */
        $totalProducts     = Product::count();
        $activeProducts    = Product::where('is_active', true)->count();
        $productsThisMonth = Product::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        /* ────────── 2. Clients ────────── */
        $totalClients        = Client::count();
        $newClientsThisMonth = Client::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        /* ────────── 3. Commandes ────────── */
        $paidStatusId = Statut::where('name', 'Livrée et payée')->value('id');

        $totalOrders  = Order::count();
        $nbPaidOrders = Order::where('statut_id', $paidStatusId)->count();

        // CA réel = somme des subtotals des articles des commandes livrées et payées
        $paidRevenue = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.statut_id', $paidStatusId)
            ->sum('order_items.subtotal');

        // Commandes du mois
        $ordersThisMonth = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Commandes par statut
        $ordersByStatus = Statut::withCount('orders')->get();

        /* ────────── 4. Avis ────────── */
        $pendingReviews  = Avis::where('approved', false)->count();
        $approvedReviews = Avis::where('approved', true)->count();
        $avgRating       = Avis::where('approved', true)->avg('rating');

        /* ────────── 5. Revenu mensuel (12 derniers mois) ────────── */
        $monthlyRevenue = Order::where('statut_id', $paidStatusId)
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_ttc) as revenue, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $revenueChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $key   = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->translatedFormat('M Y');
            $revenueChart[] = [
                'month'   => $label,
                'revenue' => $monthlyRevenue->get($key) ? $monthlyRevenue->get($key)->revenue : 0,
                'count'   => $monthlyRevenue->get($key) ? $monthlyRevenue->get($key)->count : 0,
            ];
        }

        /* ────────── 6. Top 5 produits vendus ────────── */
        $topProducts = Product::query()
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.images',
                'products.price',
                DB::raw('SUM(order_items.quantity) AS sold_qty'),
                DB::raw('SUM(order_items.subtotal) AS total_revenue')
            )
            ->join('order_items', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.statut_id', $paidStatusId)
            ->groupBy('products.id', 'products.name', 'products.slug', 'products.images', 'products.price')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->get();

        /* ────────── 7. Dernières commandes ────────── */
        $latestOrders = Order::with(['client', 'statut'])
            ->latest()
            ->limit(8)
            ->get();

        /* ────────── 8. Stock critique (< 5) ────────── */
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock', '<', 5)
            ->orderBy('stock')
            ->limit(6)
            ->get();

        return view('admin.dashborad', compact(
            'totalProducts',
            'activeProducts',
            'productsThisMonth',
            'totalClients',
            'newClientsThisMonth',
            'totalOrders',
            'nbPaidOrders',
            'paidRevenue',
            'ordersThisMonth',
            'ordersByStatus',
            'pendingReviews',
            'approvedReviews',
            'avgRating',
            'revenueChart',
            'topProducts',
            'latestOrders',
            'lowStockProducts'
        ));
    }
}