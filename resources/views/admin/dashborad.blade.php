{{-- resources/views/admin/dashborad.blade.php --}}
@extends('admin.layouts.app')

@section('css')
<style>
    /* ── Variables ── */
    :root {
        --dash-accent:  #6366f1;
        --dash-success: #10b981;
        --dash-warning: #f59e0b;
        --dash-danger:  #ef4444;
        --dash-info:    #3b82f6;
        --dash-purple:  #8b5cf6;
        --card-radius:  14px;
        --card-shadow:  0 2px 16px rgba(0,0,0,.06);
    }

    /* ── Layout ── */
    .dash-grid { display: grid; gap: 1.25rem; }

    /* ── KPI Cards ── */
    .kpi-card {
        border-radius: var(--card-radius);
        border: none;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
    .kpi-card .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
    }
    .kpi-card .kpi-value { font-size: 1.9rem; font-weight: 700; line-height: 1.1; }
    .kpi-card .kpi-label { font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; color: #9ca3af; }
    .kpi-card .kpi-badge {
        font-size: .75rem; padding: .28rem .6rem; border-radius: 999px; font-weight: 500;
    }

    /* ── Section Title ── */
    .section-title {
        font-size: .78rem; font-weight: 700; letter-spacing: .1em;
        text-transform: uppercase; color: #6b7280;
        padding-bottom: .4rem;
        border-bottom: 2px solid #f3f4f6;
        margin-bottom: 1rem;
    }

    /* ── Chart container ── */
    .chart-wrap { position: relative; }

    /* ── Status badge ── */
    .status-dot {
        display: inline-block; width: 8px; height: 8px;
        border-radius: 50%; margin-right: 6px; flex-shrink: 0;
    }

    /* ── Tables ── */
    .dash-table thead th {
        font-size: .72rem; font-weight: 700; letter-spacing: .05em;
        text-transform: uppercase; color: #9ca3af;
        background: #f9fafb; border-bottom: none; padding: .65rem 1rem;
    }
    .dash-table tbody td { padding: .75rem 1rem; vertical-align: middle; font-size: .875rem; }
    .dash-table tbody tr { transition: background .15s; }
    .dash-table tbody tr:hover { background: #f9fafb; }
    .dash-table tbody tr:last-child td { border-bottom: none; }

    /* ── Progress bar ── */
    .mini-progress { height: 6px; border-radius: 99px; background: #f3f4f6; overflow: hidden; }
    .mini-progress-bar { height: 100%; border-radius: 99px; transition: width .8s ease; }

    /* ── Rating stars ── */
    .star-fill  { color: #f59e0b; }
    .star-empty { color: #e5e7eb; }

    /* ── Low stock row ── */
    .stock-critical { background: #fff5f5 !important; }
    .stock-critical td:first-child { border-left: 3px solid var(--dash-danger); }

    /* ── Donut chart ── */
    #donutChart { display: block; }

    /* ── Responsive tweaks ── */
    @media (max-width: 767px) {
        .kpi-value { font-size: 1.5rem !important; }
    }
</style>
@endsection

@section('content')
@php
    use Illuminate\Support\Str;

    /* helpers status color — calés sur les vrais noms en BDD */
    $statusColors = [
        'annulé'                    => ['bg' => '#fee2e2', 'text' => '#991b1b'],
        'livrée et payée'           => ['bg' => '#d1fae5', 'text' => '#065f46'],
        'en cours de traitement'    => ['bg' => '#fef3c7', 'text' => '#92400e'],
        'en cours de livraison'     => ['bg' => '#dbeafe', 'text' => '#1e40af'],
    ];
    $getStatusStyle = fn($name) => $statusColors[mb_strtolower(trim($name))] ?? ['bg'=>'#f3f4f6','text'=>'#374151'];
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Tableau de bord</h4>
            <span class="text-muted small">{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
        <div class="d-flex gap-2">
            @if($pendingReviews > 0)
            <a href="{{ route('avis.index') }}" class="btn btn-sm btn-warning">
                <i class="ti ti-star me-1"></i> {{ $pendingReviews }} avis en attente
            </a>
            @endif
            @if($lowStockProducts->count() > 0)
            <span class="btn btn-sm btn-danger disabled">
                <i class="ti ti-alert-triangle me-1"></i> {{ $lowStockProducts->count() }} ruptures
            </span>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         ROW 1 – KPI CARDS (8 cartes)
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Produits --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#eef2ff">
                            <i class="ti ti-package" style="color:var(--dash-accent)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#eef2ff;color:var(--dash-accent)">
                            {{ $productsThisMonth }}+ ce mois
                        </span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-accent)">{{ number_format($totalProducts) }}</div>
                    <div class="kpi-label mt-1">Produits totaux</div>
                    <div class="mini-progress mt-2">
                        <div class="mini-progress-bar" style="background:var(--dash-accent);width:{{ $totalProducts ? round(($activeProducts/$totalProducts)*100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $totalProducts ? round(($activeProducts/$totalProducts)*100) : 0 }}% actifs</small>
                </div>
            </div>
        </div>

        {{-- Produits actifs --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#d1fae5">
                            <i class="ti ti-circle-check" style="color:var(--dash-success)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#d1fae5;color:#065f46">Actifs</span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-success)">{{ number_format($activeProducts) }}</div>
                    <div class="kpi-label mt-1">Produits actifs</div>
                </div>
            </div>
        </div>

        {{-- Clients --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#ede9fe">
                            <i class="ti ti-users" style="color:var(--dash-purple)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#ede9fe;color:#5b21b6">
                            +{{ $newClientsThisMonth }} ce mois
                        </span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-purple)">{{ number_format($totalClients) }}</div>
                    <div class="kpi-label mt-1">Clients</div>
                </div>
            </div>
        </div>

        {{-- Commandes --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#fef3c7">
                            <i class="ti ti-shopping-cart" style="color:var(--dash-warning)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#fef3c7;color:#92400e">
                            {{ $ordersThisMonth }} ce mois
                        </span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-warning)">{{ number_format($totalOrders) }}</div>
                    <div class="kpi-label mt-1">Commandes totales</div>
                </div>
            </div>
        </div>

        {{-- Commandes payées --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#d1fae5">
                            <i class="ti ti-credit-card" style="color:var(--dash-success)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#d1fae5;color:#065f46">Payées</span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-success)">{{ number_format($nbPaidOrders) }}</div>
                    <div class="kpi-label mt-1">Commandes payées</div>

                </div>
            </div>
        </div>

        {{-- Revenu encaissé --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#d1fae5">
                            <i class="ti ti-currency-dollar" style="color:var(--dash-success)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#d1fae5;color:#065f46">CA global</span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-success);font-size:1.4rem">
                        {{ number_format($paidRevenue, 2, ',', ' ') }}
                    </div>
                    <div class="kpi-label mt-1">Revenu encaissé (TND)</div>
                </div>
            </div>
        </div>

        {{-- Avis approuvés --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#fef3c7">
                            <i class="ti ti-star" style="color:var(--dash-warning)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#fef3c7;color:#92400e">
                            ★ {{ number_format($avgRating ?? 0, 1) }}
                        </span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-warning)">{{ number_format($approvedReviews) }}</div>
                    <div class="kpi-label mt-1">Avis approuvés</div>
                </div>
            </div>
        </div>

        {{-- Stock critique --}}
        <div class="col-6 col-md-4 col-xl-3">
            <div class="card kpi-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="kpi-icon" style="background:#fee2e2">
                            <i class="ti ti-alert-triangle" style="color:var(--dash-danger)"></i>
                        </div>
                        <span class="kpi-badge" style="background:#fee2e2;color:#991b1b">Critique</span>
                    </div>
                    <div class="kpi-value" style="color:var(--dash-danger)">{{ $lowStockProducts->count() }}</div>
                    <div class="kpi-label mt-1">Produits stock &lt; 5</div>
                </div>
            </div>
        </div>

    </div>{{-- /row KPIs --}}

    {{-- ═══════════════════════════════════════════
         ROW 2 – CHART + STATUS DONUT
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Graphique revenu mensuel --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3">
                    <p class="section-title mb-3">Revenu mensuel – 12 derniers mois</p>
                    <div class="chart-wrap" style="height:240px">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Commandes par statut --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3">
                    <p class="section-title">Commandes par statut</p>
                    <canvas id="donutChart" style="max-height:180px"></canvas>
                    <div class="mt-3">
                        @foreach($ordersByStatus as $s)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <span class="status-dot" style="background: {{ ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6'][$loop->index % 5] }}"></span>
                                <span class="small">{{ $s->name }}</span>
                            </div>
                            <span class="fw-semibold small">{{ $s->orders_count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row charts --}}

    {{-- ═══════════════════════════════════════════
         ROW 3 – TOP PRODUITS + DERNIÈRES COMMANDES
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Top 5 produits --}}
        <div class="col-12 col-xl-5">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="p-3 pb-0">
                    <p class="section-title">Top 5 Produits vendus</p>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produit</th>
                                <th class="text-end">Qté</th>
                                <th class="text-end">CA (TND)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $p)
                            @php
                                $firstImage = (!empty($p->images) && is_array($p->images))
                                    ? asset('storage/'.$p->images[0])
                                    : asset('assets/img/placeholder-100x100.png');
                            @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold text-muted">#{{ $index+1 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $firstImage }}" width="36" height="36"
                                            class="rounded-2 object-fit-cover" alt="{{ $p->name }}">
                                        <div>
                                            <div class="fw-medium small">{{ Str::limit($p->name, 28) }}</div>
                                            <div class="text-muted" style="font-size:.7rem">PROD{{ str_pad($p->id, 4,'0',STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">{{ $p->sold_qty }}</td>
                                <td class="text-end">
                                    <span class="fw-bold" style="color:var(--dash-success)">
                                        {{ number_format($p->total_revenue ?? $p->sold_qty * $p->price, 2, ',', ' ') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('produits.edit', $p) }}" class="btn btn-sm btn-outline-primary waves-effect" style="font-size:.72rem">
                                        <i class="ti ti-pencil me-1"></i>Gérer
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="ti ti-mood-sad d-block mb-1" style="font-size:1.5rem"></i>
                                    Aucune vente enregistrée
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dernières commandes --}}
        <div class="col-12 col-xl-7">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3 pb-0 d-flex justify-content-between align-items-center">
                    <p class="section-title mb-0">Dernières commandes</p>
                    <a href="{{ route('commandes.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem">Voir tout</a>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Client</th>
                                <th class="text-end">Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $order)
                            @php $st = $getStatusStyle($order->statut?->name ?? '') @endphp
                            <tr>
                                <td class="fw-medium small">{{ $order->numero_commande }}</td>
                                <td>
                                    <div class="fw-medium small">{{ Str::limit($order->client?->name ?? '—', 20) }}</div>
                                    <div class="text-muted" style="font-size:.7rem">{{ $order->client?->phone }}</div>
                                </td>
                                <td class="text-end fw-semibold small">
                                    {{ number_format($order->total_ttc, 2, ',', ' ') }} TND
                                </td>
                                <td>
                                    <span class="px-2 py-1 rounded-2 small fw-medium"
                                        style="background:{{ $st['bg'] }};color:{{ $st['text'] }};font-size:.72rem">
                                        {{ $order->statut?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('d/m/y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucune commande</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ═══════════════════════════════════════════
         ROW 4 – STOCK CRITIQUE
    ═══════════════════════════════════════════ --}}
    @if($lowStockProducts->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3 pb-0">
                    <p class="section-title">
                        <i class="ti ti-alert-triangle me-1" style="color:var(--dash-danger)"></i>
                        Stock critique (moins de 5 unités)
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th class="text-center">Stock restant</th>
                                <th>Niveau</th>
                                <th>Prix</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $p)
                            <tr class="{{ $p->stock == 0 ? 'stock-critical' : '' }}">
                                <td>
                                    @php
                                        $img = (!empty($p->images) && is_array($p->images))
                                            ? asset('storage/'.$p->images[0])
                                            : asset('assets/img/placeholder-100x100.png');
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $img }}" width="32" height="32" class="rounded-2 object-fit-cover">
                                        <span class="small fw-medium">{{ Str::limit($p->name, 35) }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $p->stock == 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $p->stock }}
                                    </span>
                                </td>
                                <td>
                                    @php $pct = min(100, ($p->stock / 5) * 100) @endphp
                                    <div class="mini-progress" style="width:80px">
                                        <div class="mini-progress-bar"
                                            style="width:{{ $pct }}%;background:{{ $p->stock == 0 ? 'var(--dash-danger)' : 'var(--dash-warning)' }}">
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ number_format($p->price, 2, ',', ' ') }} TND</td>
                                <td>
                                    <a href="{{ route('produits.edit', $p) }}"
                                        class="btn btn-sm btn-outline-primary" style="font-size:.72rem">
                                        <i class="ti ti-pencil me-1"></i>Gérer
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>{{-- /container --}}
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Données depuis Blade ── */
    const revenueData = {!! json_encode($revenueChart) !!};
    const statusData  = {!! json_encode($ordersByStatus->map(fn($s) => ['name' => $s->name, 'orders_count' => $s->orders_count])) !!};

    /* ────────────────────────────────────────
       Graphique barres – Revenu mensuel
    ──────────────────────────────────────── */
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: revenueData.map(d => d.month),
                datasets: [
                    {
                        label: 'Revenu (TND)',
                        data: revenueData.map(d => parseFloat(d.revenue)),
                        backgroundColor: 'rgba(99, 102, 241, 0.18)',
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        borderRadius: 6,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Commandes',
                        data: revenueData.map(d => parseInt(d.count)),
                        type: 'line',
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,.12)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0
                                ? ' ' + Number(ctx.raw).toLocaleString('fr-TN') + ' TND'
                                : ' ' + ctx.raw + ' commandes'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {
                        position: 'left',
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 10 }, callback: v => v.toLocaleString('fr-TN') + ' DT' }
                    },
                    y1: {
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    /* ────────────────────────────────────────
       Donut – Statuts commandes
    ──────────────────────────────────────── */
    const ctxDonut = document.getElementById('donutChart');
    if (ctxDonut && statusData.length) {
        const palette = ['#10b981','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: statusData.map(s => s.name),
                datasets: [{
                    data: statusData.map(s => s.orders_count),
                    backgroundColor: palette.slice(0, statusData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + ctx.label + ' : ' + ctx.raw + ' commandes'
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

});
</script>
@endsection