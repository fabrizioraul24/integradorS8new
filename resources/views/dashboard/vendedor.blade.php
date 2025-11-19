@extends('layouts.sidebar-vendedor')

@section('title', 'Vendedor | Pil Andina')
@section('page-title', 'Resumen de Vendedor')

@section('content')
    @php
        use App\Models\Sale;
        use App\Models\Company;
        use App\Models\VendorVisit;
        use Carbon\Carbon;
        $userId = Auth::id();
        $startMonth = Carbon::now()->startOfMonth();
        $salesMonth = Sale::where('seller_id', $userId)->whereDate('created_at', '>=', $startMonth);
        $countSales = $salesMonth->count();
        $amountMonth = $salesMonth->sum('total_amount');
        $clientsCount = Company::count();
        $pendingVisits = VendorVisit::where('user_id', $userId)->whereDate('visit_date', '>=', now()->toDateString())->count();
        $saleTypeLabels = [
            'empresa_institucional' => 'Empresas',
            'tienda_barrio' => 'Tiendas',
            'comprador_minorista' => 'Minoristas',
        ];

        $last7 = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $daily = Sale::where('seller_id', $userId)->whereDate('created_at', $date)->sum('total_amount');
            $last7->push(['date' => Carbon::parse($date)->format('d/m'), 'value' => (float) $daily]);
        }

        $clients7 = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $daily = Company::where('created_by', $userId)->whereDate('created_at', $date)->count();
            $clients7->push(['date' => Carbon::parse($date)->format('d/m'), 'value' => $daily]);
        }

        $typeSummary = Sale::where('seller_id', $userId)
            ->selectRaw('sale_type, COUNT(*) as total')
            ->groupBy('sale_type')
            ->get()
            ->map(function ($row) use ($saleTypeLabels) {
                return [
                    'label' => $saleTypeLabels[$row->sale_type] ?? ucfirst(str_replace('_', ' ', $row->sale_type ?? 'Otros')),
                    'value' => (int) $row->total,
                ];
            });

        if ($typeSummary->isEmpty()) {
            $typeSummary = collect([['label' => 'Sin datos', 'value' => 1]]);
        }
    @endphp

    <div class="stats-grid">
        <div class="card">
            <h3>Ventas mes</h3>
            <div class="value">{{ $countSales }}</div>
            <span class="chip text-white/70"><i class="ri-bar-chart-grouped-line"></i> Registradas</span>
        </div>
        <div class="card">
            <h3>Monto mes</h3>
            <div class="value">Bs {{ number_format($amountMonth, 2) }}</div>
            <span class="chip text-green-300"><i class="ri-money-dollar-circle-line"></i> Total</span>
        </div>
        <div class="card">
            <h3>Clientes</h3>
            <div class="value">{{ $clientsCount }}</div>
            <span class="chip text-blue-200"><i class="ri-user-3-line"></i> Asignados</span>
        </div>
        <div class="card">
            <h3>Visitas pendientes</h3>
            <div class="value">{{ $pendingVisits }}</div>
            <span class="chip text-white/70"><i class="ri-calendar-event-line"></i> Agenda</span>
        </div>
    </div>

    <div class="chart-grid" style="margin-top:1rem; display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1rem;">
        <div class="card">
            <div class="chart-head">
                <h4>Ventas últimos 7 días</h4>
                <span class="chip text-white/70">Bs</span>
            </div>
            <canvas id="salesChart" height="160"></canvas>
        </div>
        <div class="card">
            <div class="chart-head">
                <h4>Clientes registrados</h4>
                <span class="chip text-white/70"># clientes</span>
            </div>
            <canvas id="clientsChart" height="160"></canvas>
        </div>
        <div class="card">
            <div class="chart-head">
                <h4>Tipos de venta</h4>
                <span class="chip text-white/70">Último mes</span>
            </div>
            <canvas id="typesChart" height="160"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const salesData = @json($last7);
    const clientsData = @json($clients7);
    const typeData = @json($typeSummary);

    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: salesData.map(d => d.date),
                datasets: [{
                    label: 'Bs',
                    data: salesData.map(d => d.value),
                    backgroundColor: 'rgba(134,172,212,0.6)',
                    borderColor: 'rgba(78,107,175,0.9)',
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#fff' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.08)' },
                    }
                }
            }
        });
    }

    const clientsCtx = document.getElementById('clientsChart')?.getContext('2d');
    if (clientsCtx) {
        new Chart(clientsCtx, {
            type: 'line',
            data: {
                labels: clientsData.map(d => d.date),
                datasets: [{
                    label: '# clientes',
                    data: clientsData.map(d => d.value),
                    fill: false,
                    tension: 0.3,
                    backgroundColor: 'rgba(78,107,175,0.9)',
                    borderColor: 'rgba(134,172,212,0.8)',
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(78,107,175,0.9)',
                    pointRadius: 4,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#fff' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision:0, color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.08)' },
                    }
                }
            }
        });
    }

    const typesCtx = document.getElementById('typesChart')?.getContext('2d');
    if (typesCtx) {
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: typeData.map(d => d.label),
                datasets: [{
                    data: typeData.map(d => d.value),
                    backgroundColor: ['#4e6baf', '#86acd4', '#fbbf24', '#22c55e'],
                    borderColor: '#1f2a44',
                    borderWidth: 1,
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom', labels: { color: '#fff' } } }
            }
        });
    }
</script>
@endpush
