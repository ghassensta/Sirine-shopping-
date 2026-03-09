<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SiteAnalytics;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Client;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AnalyticsController extends Controller
{
    private static function tableExists(): bool
    {
        return Schema::hasTable('site_analytics');
    }

    /**
     * Scope réutilisable — uniquement statut_id = 2 (Livrée et payée)
     */
    private function paidOrders()
    {
        return Order::where('statut_id', 2);
    }

    // ══════════════════════════════════════════════════════════
    // TRACKING
    // ══════════════════════════════════════════════════════════

    public function track(Request $request)
    {
        if (!self::tableExists()) {
            return response()->json(['status' => 'skip'], 200);
        }

        $request->validate([
            'page'    => 'required|string|max:500',
            'action'  => 'nullable|string|max:100',
            'details' => 'nullable|array',
        ]);

        $visitorId = $request->cookie('sc_visitor_id')
            ?? $request->header('X-Visitor-ID')
            ?? (string) Str::uuid();

        try {
            SiteAnalytics::create([
                'visitor_id' => $visitorId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'page'       => $request->input('page'),
                'action'     => $request->input('action', 'page_view'),
                'details'    => $request->input('details', []),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Analytics track failed: ' . $e->getMessage());
        }

        return response()->json(['status' => 'ok', 'visitor_id' => $visitorId])
            ->cookie('sc_visitor_id', $visitorId, 60 * 24 * 365);
    }

    public static function trackPageView(Request $request): void
    {
        try {
            if (!self::tableExists()) return;

            $path = $request->path();
            if (
                Str::startsWith($path, ['admin/', 'api/', '_debugbar', 'livewire']) ||
                Str::endsWith($path, ['.css', '.js', '.png', '.jpg', '.ico', '.xml', '.txt'])
            ) {
                return;
            }

            $visitorId = $request->cookie('sc_visitor_id') ?? (string) Str::uuid();

            SiteAnalytics::create([
                'visitor_id' => $visitorId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'page'       => '/' . $path,
                'action'     => 'page_view',
                'details'    => [
                    'referrer' => $request->header('referer'),
                    'method'   => $request->method(),
                ],
            ]);
        } catch (\Exception $e) {
            \Log::warning('Analytics trackPageView failed: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // DASHBOARD ANALYTICS
    // ══════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        if (!self::tableExists()) {
            return view('admin.analytics.index', $this->emptyData($request->input('period', '30')));
        }

        $period = $request->input('period', '30');
        $from   = now()->subDays((int) $period)->startOfDay();
        $to     = now()->endOfDay();

        try {
            $totalVisits    = SiteAnalytics::whereBetween('created_at', [$from, $to])->count();

            // FIX: Comptage correct des visiteurs uniques
            $uniqueVisitors = SiteAnalytics::whereBetween('created_at', [$from, $to])
                                ->distinct()
                                ->count('visitor_id');

            // statut_id = 2 uniquement
            $totalOrders  = $this->paidOrders()->whereBetween('created_at', [$from, $to])->count();
            $totalRevenue = $this->paidOrders()->whereBetween('created_at', [$from, $to])->sum('total_ttc');

            $newClients     = Client::whereBetween('created_at', [$from, $to])->count();
            $conversionRate = $uniqueVisitors > 0
                                ? round(($totalOrders / $uniqueVisitors) * 100, 2)
                                : 0;

            // FIX: Visites par jour avec tous les jours (même ceux sans données)
            $visitsByDay = $this->getVisitsByDayWithAllDates($from, $to);

            $topPages = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->where('action', 'page_view')
                ->selectRaw('page, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_views')
                ->groupBy('page')
                ->orderByDesc('views')
                ->limit(10)
                ->get();

            $topProducts = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->where('page', 'like', '/article/%')
                ->selectRaw('page, COUNT(*) as views')
                ->groupBy('page')
                ->orderByDesc('views')
                ->limit(10)
                ->get()
                ->map(function ($row) {
                    $slug    = Str::afterLast($row->page, '/');
                    $product = Product::where('slug', $slug)->first(['id', 'name', 'price', 'image_avant']);
                    return ['page' => $row->page, 'views' => $row->views, 'product' => $product];
                });

            $topCategories = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->where('page', 'like', '/collections/%')
                ->selectRaw('page, COUNT(*) as views')
                ->groupBy('page')
                ->orderByDesc('views')
                ->limit(8)
                ->get()
                ->map(function ($row) {
                    $slug     = Str::afterLast($row->page, '/');
                    $category = Category::where('slug', $slug)->first(['id', 'name']);
                    return ['page' => $row->page, 'views' => $row->views, 'category' => $category];
                });

            $trafficSources = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->whereNotNull('details')
                ->get(['details'])
                ->map(function ($row) {
                    $details  = is_array($row->details) ? $row->details : json_decode($row->details, true);
                    $referrer = $details['referrer'] ?? null;
                    return $referrer ?: 'Direct';
                })
                ->groupBy(fn($r) => $r)
                ->map(fn($group) => $group->count())
                ->sortDesc()
                ->take(8);

            // FIX: Revenu par jour avec tous les jours
            $revenueByDay = $this->getRevenueByDayWithAllDates($from, $to);

            $devices = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->selectRaw('user_agent, COUNT(*) as count')
                ->groupBy('user_agent')
                ->get()
                ->groupBy(function ($row) {
                    $ua = strtolower($row->user_agent ?? '');
                    if (Str::contains($ua, ['mobile', 'android', 'iphone', 'ipad'])) return 'Mobile';
                    if (Str::contains($ua, ['tablet'])) return 'Tablette';
                    return 'Desktop';
                })
                ->map(fn($group) => $group->sum('count'));

            $recentActions = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->where('action', '!=', 'page_view')
                ->latest()
                ->limit(20)
                ->get();

            $peakHours = SiteAnalytics::whereBetween('created_at', [$from, $to])
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as visits')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->keyBy('hour');

        } catch (\Exception $e) {
            \Log::error('Analytics index error: ' . $e->getMessage());
            return view('admin.analytics.index', $this->emptyData($period));
        }

        return view('admin.analytics.index', compact(
            'period', 'from', 'to',
            'totalVisits', 'uniqueVisitors', 'totalOrders', 'totalRevenue',
            'newClients', 'conversionRate',
            'visitsByDay', 'topPages', 'topProducts', 'topCategories',
            'trafficSources', 'revenueByDay', 'devices', 'recentActions', 'peakHours'
        ));
    }

    /**
     * FIX: Générer les visites par jour avec TOUS les jours (même ceux à 0)
     */
    private function getVisitsByDayWithAllDates($from, $to)
    {
        // Récupérer les données réelles
        $actualData = SiteAnalytics::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as visits, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Générer tous les jours de la période
        $period = CarbonPeriod::create($from, $to);
        $allDays = collect();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $allDays->push((object)[
                'date' => $dateStr,
                'visits' => $actualData->get($dateStr)->visits ?? 0,
                'unique_visitors' => $actualData->get($dateStr)->unique_visitors ?? 0,
            ]);
        }

        return $allDays;
    }

    /**
     * FIX: Générer le revenu par jour avec TOUS les jours (même ceux à 0)
     */
    private function getRevenueByDayWithAllDates($from, $to)
    {
        // Récupérer les données réelles
        $actualData = $this->paidOrders()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, SUM(total_ttc) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Générer tous les jours de la période
        $period = CarbonPeriod::create($from, $to);
        $allDays = collect();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $allDays->push((object)[
                'date' => $dateStr,
                'revenue' => $actualData->get($dateStr)->revenue ?? 0,
                'orders' => $actualData->get($dateStr)->orders ?? 0,
            ]);
        }

        return $allDays;
    }

    // ══════════════════════════════════════════════════════════
    // API JSON
    // ══════════════════════════════════════════════════════════

    public function apiOverview(Request $request)
    {
        if (!self::tableExists()) {
            return response()->json(['visits' => 0, 'unique_visitors' => 0, 'orders' => 0, 'revenue' => 0, 'new_clients' => 0]);
        }

        $days = (int) $request->input('days', 30);
        $from = now()->subDays($days)->startOfDay();

        return response()->json([
            'visits'          => SiteAnalytics::where('created_at', '>=', $from)->count(),
            'unique_visitors' => SiteAnalytics::where('created_at', '>=', $from)->distinct()->count('visitor_id'),
            // statut_id = 2 uniquement
            'orders'          => Order::where('statut_id', 2)->where('created_at', '>=', $from)->count(),
            'revenue'         => round(Order::where('statut_id', 2)->where('created_at', '>=', $from)->sum('total_ttc'), 3),
            'new_clients'     => Client::where('created_at', '>=', $from)->count(),
        ]);
    }

    public function apiVisitsChart(Request $request)
    {
        if (!self::tableExists()) {
            return response()->json(['labels' => [], 'visits' => [], 'unique_visitors' => []]);
        }

        $days = (int) $request->input('days', 30);
        $from = now()->subDays($days)->startOfDay();

        $data = SiteAnalytics::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as visits, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels'          => $data->pluck('date'),
            'visits'          => $data->pluck('visits'),
            'unique_visitors' => $data->pluck('unique_visitors'),
        ]);
    }

    public function apiRevenueChart(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $from = now()->subDays($days)->startOfDay();

        // statut_id = 2 uniquement
        $data = Order::where('statut_id', 2)
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, SUM(total_ttc) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels'  => $data->pluck('date'),
            'revenue' => $data->pluck('revenue'),
            'orders'  => $data->pluck('orders'),
        ]);
    }

    public function apiTopPages(Request $request)
    {
        if (!self::tableExists()) {
            return response()->json([]);
        }

        $days = (int) $request->input('days', 30);
        $from = now()->subDays($days)->startOfDay();

        $data = SiteAnalytics::where('created_at', '>=', $from)
            ->where('action', 'page_view')
            ->selectRaw('page, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_views')
            ->groupBy('page')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        return response()->json($data);
    }

    // ══════════════════════════════════════════════════════════
    // EXPORT CSV
    // ══════════════════════════════════════════════════════════

    public function export(Request $request)
    {
        if (!self::tableExists()) {
            return back()->with('error', 'Table analytics introuvable. Lancez php artisan migrate.');
        }

        $days = (int) $request->input('days', 30);
        $from = now()->subDays($days)->startOfDay();
        $rows = SiteAnalytics::where('created_at', '>=', $from)
            ->orderByDesc('created_at')
            ->get(['visitor_id', 'ip_address', 'page', 'action', 'created_at']);

        $filename = 'analytics_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Visitor ID', 'IP', 'Page', 'Action', 'Date']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->visitor_id,
                    $row->ip_address,
                    $row->page,
                    $row->action,
                    $row->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ══════════════════════════════════════════════════════════
    // PURGE - FIX: Amélioration de la méthode de purge
    // ══════════════════════════════════════════════════════════

    public function purge(Request $request)
    {
        if (!self::tableExists()) {
            return back()->with('error', 'Table analytics introuvable. Lancez php artisan migrate.');
        }

        try {
            $request->validate([
                'days' => 'required|integer|min:7|max:365'
            ]);

            $days = (int) $request->input('days');
            $cutoff = now()->subDays($days)->endOfDay();

            \Log::info("Analytics purge attempt - Deleting records older than: " . $cutoff->format('Y-m-d H:i:s'));

            $deleted = SiteAnalytics::where('created_at', '<', $cutoff)->delete();

            \Log::info("Analytics purge completed - Deleted {$deleted} records");

            return back()->with('success', "{$deleted} enregistrement(s) supprimé(s) avec succès.");

        } catch (\Exception $e) {
            \Log::error('Analytics purge error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la purge : ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════════════════
    // HELPER — données vides si table absente
    // ══════════════════════════════════════════════════════════

    private function emptyData(string $period): array
    {
        $from = now()->subDays((int) $period)->startOfDay();
        $to = now()->endOfDay();

        // Générer des jours vides pour la période
        $emptyDays = collect();
        $periodRange = CarbonPeriod::create($from, $to);

        foreach ($periodRange as $date) {
            $emptyDays->push((object)[
                'date' => $date->format('Y-m-d'),
                'visits' => 0,
                'unique_visitors' => 0,
            ]);
        }

        return [
            'period'         => $period,
            'from'           => $from,
            'to'             => $to,
            'totalVisits'    => 0,
            'uniqueVisitors' => 0,
            // statut_id = 2 uniquement
            'totalOrders'    => Order::where('statut_id', 2)->count(),
            'totalRevenue'   => Order::where('statut_id', 2)->sum('total_ttc'),
            'newClients'     => 0,
            'conversionRate' => 0,
            'visitsByDay'    => $emptyDays,
            'topPages'       => collect(),
            'topProducts'    => collect(),
            'topCategories'  => collect(),
            'trafficSources' => collect(),
            'revenueByDay'   => collect(),
            'devices'        => collect(),
            'recentActions'  => collect(),
            'peakHours'      => collect(),
        ];
    }
}
