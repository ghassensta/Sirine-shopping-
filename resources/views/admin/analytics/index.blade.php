{{-- resources/views/admin/analytics/index.blade.php --}}
@extends('admin.layouts.app')

@section('css')
<style>
    /* ── Variables (même thème que dashborad.blade.php) ── */
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

    /* ── KPI Cards (identiques dashboard) ── */
    .kpi-card {
        border-radius: var(--card-radius);
        border: none;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(0,0,0,.10); }
    .kpi-card .kpi-icon {
        width: 52px; height: 52px; border-radius: 12px;
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

    /* ── Chart container ── */
    .chart-wrap { position: relative; }

    /* ── Status dot ── */
    .status-dot {
        display: inline-block; width: 8px; height: 8px;
        border-radius: 50%; margin-right: 6px; flex-shrink: 0;
    }

    /* ── Period selector ── */
    .period-btn {
        font-size: .78rem; padding: .3rem .75rem; border-radius: 8px;
        border: 1.5px solid #e5e7eb; background: white; color: #6b7280;
        cursor: pointer; transition: all .15s; font-weight: 500;
    }
    .period-btn:hover, .period-btn.active {
        background: var(--dash-accent); color: white; border-color: var(--dash-accent);
    }

    /* ── Device badge ── */
    .device-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .device-row .device-label { font-size: .8rem; min-width: 70px; color: #374151; font-weight: 500; }
    .device-row .device-bar { flex: 1; }
    .device-row .device-count { font-size: .78rem; font-weight: 700; color: #374151; min-width: 36px; text-align: right; }

    /* ── Traffic source pill ── */
    .source-pill {
        display: flex; justify-content: space-between; align-items: center;
        padding: .45rem .75rem; border-radius: 9px; margin-bottom: 6px;
        background: #f9fafb; font-size: .8rem;
    }
    .source-pill .source-name { font-weight: 500; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
    .source-pill .source-count { font-weight: 700; color: var(--dash-accent); }

    /* ── Peak hours heatmap ── */
    .hour-cell {
        display: inline-flex; align-items: center; justify-content: center;
        width: 34px; height: 34px; border-radius: 7px;
        font-size: .7rem; font-weight: 700; margin: 2px;
        transition: transform .15s;
    }
    .hour-cell:hover { transform: scale(1.12); cursor: default; }

    /* ── Page row ── */
    .page-url {
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: .73rem; color: #6366f1;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 260px; display: block;
    }

    /* ── Responsive ── */
    @media(max-width:767px) {
        .kpi-value { font-size: 1.5rem !important; }
        .page-url  { max-width: 160px; }
    }
</style>
@endsection

@section('content')
@php
    $palette = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#3b82f6','#ec4899','#14b8a6'];

    // Calcul max pour les barres relatives
    $maxPageViews   = $topPages->max('views') ?: 1;
    $maxSourceCount = $trafficSources->max() ?: 1;
    $maxHourVisits  = $peakHours->max('visits') ?: 1;
    $totalDevices   = $devices->sum() ?: 1;

    // Couleur de cellule heure de pointe (dégradé blanc → indigo)
    $heatColor = function(int $visits) use ($maxHourVisits): string {
        $ratio = $visits / $maxHourVisits;
        $r = (int)(238 + (99  - 238) * $ratio);
        $g = (int)(238 + (102 - 238) * $ratio);
        $b = (int)(238 + (241 - 238) * $ratio);
        $textColor = $ratio > 0.5 ? 'white' : '#374151';
        return "background:rgb({$r},{$g},{$b});color:{$textColor}";
    };
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ── Header ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Analytics</h4>
            <span class="text-muted small">{{ now()->translatedFormat('l d F Y') }} · Période : {{ $period }} jours</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Sélecteur de période --}}
            @foreach([7 => '7j', 14 => '14j', 30 => '30j', 90 => '90j'] as $d => $label)
            <a href="{{ route('analytics.index', ['period' => $d]) }}"
               class="period-btn {{ (int)$period === $d ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
            {{-- Export CSV --}}
            <a href="{{ route('analytics.export', ['days' => $period]) }}"
               class="btn btn-sm btn-outline-success">
                <i class="ti ti-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         ROW 2 – GRAPHIQUE VISITES + REVENU
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Visites par jour --}}
        <div class="col-12 col-xl-7">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3">
                    <p class="section-title mb-3">Visites & Visiteurs uniques — {{ $period }} derniers jours</p>
                    <div class="chart-wrap" style="height:230px">
                        <canvas id="visitsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenu par jour --}}
        <div class="col-12 col-xl-5">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="card-body p-3">
                    <p class="section-title mb-3">Revenu & Commandes — {{ $period }} derniers jours</p>
                    <div class="chart-wrap" style="height:230px">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row charts --}}

    {{-- ═══════════════════════════════════════════
         ROW 3 – TOP PAGES + SOURCES TRAFIC
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Top Pages --}}
        <div class="col-12 col-xl-7">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="p-3 pb-0">
                    <p class="section-title">Top 10 pages visitées</p>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Page</th>
                                <th class="text-end">Vues</th>
                                <th class="text-end">Uniques</th>
                                <th style="min-width:100px">Popularité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPages as $index => $page)
                            <tr>
                                <td><span class="fw-bold text-muted">#{{ $index + 1 }}</span></td>
                                <td>
                                    <span class="page-url" title="{{ $page->page }}">{{ $page->page }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($page->views) }}</td>
                                <td class="text-end text-muted small">{{ number_format($page->unique_views) }}</td>
                                <td>
                                    <div class="mini-progress">
                                        <div class="mini-progress-bar"
                                             style="background:var(--dash-accent);width:{{ round(($page->views / $maxPageViews) * 100) }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="ti ti-mood-sad d-block mb-1" style="font-size:1.5rem"></i>
                                    Aucune donnée disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sources de trafic + Appareils --}}
        <div class="col-12 col-xl-5">
            <div class="row g-3 h-100">

                {{-- Sources --}}
                <div class="col-12">
                    <div class="card" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                        <div class="card-body p-3">
                            <p class="section-title">Sources de trafic</p>
                            @forelse($trafficSources as $source => $count)
                            <div class="source-pill">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="status-dot" style="background:{{ $palette[$loop->index % count($palette)] }}"></span>
                                    <span class="source-name" title="{{ $source }}">
                                        {{ $source && $source !== 'Direct' ? parse_url($source, PHP_URL_HOST) ?? $source : 'Direct / Bookmark' }}
                                    </span>
                                </div>
                                <span class="source-count">{{ number_format($count) }}</span>
                            </div>
                            @empty
                            <p class="text-muted small text-center py-2">Aucune donnée</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Appareils --}}
                <div class="col-12">
                    <div class="card" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                        <div class="card-body p-3">
                            <p class="section-title">Appareils</p>
                            @foreach($devices as $device => $count)
                            @php
                                $deviceColors = ['Mobile' => 'var(--dash-success)', 'Desktop' => 'var(--dash-accent)', 'Tablette' => 'var(--dash-warning)'];
                                $pct = round(($count / $totalDevices) * 100);
                            @endphp
                            <div class="device-row">
                                <span class="device-label">
                                    {{ $device === 'Mobile' ? '📱' : ($device === 'Desktop' ? '🖥️' : '📟') }}
                                    {{ $device }}
                                </span>
                                <div class="device-bar">
                                    <div class="mini-progress">
                                        <div class="mini-progress-bar"
                                             style="background:{{ $deviceColors[$device] ?? 'var(--dash-info)' }};width:{{ $pct }}%">
                                        </div>
                                    </div>
                                </div>
                                <span class="device-count">{{ $pct }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- /row --}}

    {{-- ═══════════════════════════════════════════
         ROW 4 – TOP PRODUITS + TOP CATÉGORIES
    ═══════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Top Produits vus --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="p-3 pb-0">
                    <p class="section-title">Top produits consultés</p>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produit</th>
                                <th class="text-end">Vues</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $index => $row)
                            <tr>
                                <td><span class="fw-bold text-muted">#{{ $index + 1 }}</span></td>
                                <td>
                                    @if($row['product'])
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $row['product']->image_avant ? asset('storage/'.$row['product']->image_avant) : asset('assets/img/placeholder-100x100.png') }}"
                                             width="34" height="34" class="rounded-2 object-fit-cover">
                                        <div>
                                            <div class="fw-medium small">{{ \Illuminate\Support\Str::limit($row['product']->name, 26) }}</div>
                                            <div class="text-muted" style="font-size:.7rem">{{ number_format($row['product']->price, 2, ',', ' ') }} TND</div>
                                        </div>
                                    </div>
                                    @else
                                    <span class="page-url small">{{ $row['page'] }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($row['views']) }}</td>
                                <td>
                                    @if($row['product'])
                                    <a href="{{ route('produits.edit', $row['product']) }}"
                                       class="btn btn-sm btn-outline-primary" style="font-size:.72rem">
                                        <i class="ti ti-pencil me-1"></i>Gérer
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="ti ti-mood-sad d-block mb-1" style="font-size:1.5rem"></i>
                                    Aucune visite produit
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Catégories --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100" style="border-radius:var(--card-radius);box-shadow:var(--card-shadow);border:none">
                <div class="p-3 pb-0">
                    <p class="section-title">Top catégories visitées</p>
                </div>
                <div class="table-responsive">
                    <table class="table dash-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Catégorie</th>
                                <th class="text-end">Vues</th>
                                <th>Popularité</th>
                            </tr>
                        </thead>
                        @php $maxCatViews = collect($topCategories)->max('views') ?: 1; @endphp
                        <tbody>
                            @forelse($topCategories as $index => $row)
                            <tr>
                                <td><span class="fw-bold text-muted">#{{ $index + 1 }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-dot" style="background:{{ $palette[$index % count($palette)] }}"></span>
                                        <span class="fw-medium small">
                                            {{ $row['category']?->name ?? \Illuminate\Support\Str::afterLast($row['page'], '/') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($row['views']) }}</td>
                                <td>
                                    <div class="mini-progress" style="width:90px">
                                        <div class="mini-progress-bar"
                                             style="background:{{ $palette[$index % count($palette)] }};width:{{ round(($row['views'] / $maxCatViews) * 100) }}%">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucune donnée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>{{-- /row --}}
</div>{{-- /container --}}
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Données Blade → JS ── */
    const visitsByDay  = {!! json_encode($visitsByDay->map(fn($r) => ['date' => $r->date, 'visits' => $r->visits, 'unique' => $r->unique_visitors])) !!};
    const revenueByDay = {!! json_encode($revenueByDay->map(fn($r) => ['date' => $r->date, 'revenue' => $r->revenue, 'orders' => $r->orders])) !!};

    const defaultOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 10 } },
        }
    };

    /* ────────────────────────────────────────
       Graphique Visites & Visiteurs uniques
    ──────────────────────────────────────── */
    const ctxVisits = document.getElementById('visitsChart');
    if (ctxVisits) {
        new Chart(ctxVisits, {
            type: 'bar',
            data: {
                labels: visitsByDay.map(d => d.date),
                datasets: [
                    {
                        label: 'Visites totales',
                        data: visitsByDay.map(d => d.visits),
                        backgroundColor: 'rgba(99,102,241,.18)',
                        borderColor: '#6366f1',
                        borderWidth: 2,
                        borderRadius: 5,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Visiteurs uniques',
                        data: visitsByDay.map(d => d.unique),
                        type: 'line',
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,.10)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#8b5cf6',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                ...defaultOpts,
                scales: {
                    ...defaultOpts.scales,
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } }
                }
            }
        });
    }

    /* ────────────────────────────────────────
       Graphique Revenu & Commandes
    ──────────────────────────────────────── */
    const ctxRevenue = document.getElementById('revenueChart');
    if (ctxRevenue) {
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: revenueByDay.map(d => d.date),
                datasets: [
                    {
                        label: 'Revenu (TND)',
                        data: revenueByDay.map(d => parseFloat(d.revenue)),
                        backgroundColor: 'rgba(16,185,129,.18)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderRadius: 5,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Commandes',
                        data: revenueByDay.map(d => parseInt(d.orders)),
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245,158,11,.10)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#f59e0b',
                        tension: 0.4,
                        fill: false,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                ...defaultOpts,
                scales: {
                    x: defaultOpts.scales.x,
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
                },
                plugins: {
                    ...defaultOpts.plugins,
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0
                                ? ' ' + Number(ctx.raw).toLocaleString('fr-TN') + ' TND'
                                : ' ' + ctx.raw + ' commandes'
                        }
                    }
                }
            }
        });
    }

});
</script>
@endsection
