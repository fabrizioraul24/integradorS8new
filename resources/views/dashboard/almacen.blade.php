@extends('layouts.sidebar-almacen')

@section('title', 'Almacén | Pil Andina')
@section('page-title', 'Tablero de almacén')

@section('content')
    <div class="stats-grid">
        <div class="card">
            <h3>Stock disponible</h3>
            <div class="value">{{ number_format($stats['stock'] ?? 0) }} uds</div>
            <span class="chip text-white/70"><i class="ri-dropbox-line"></i> Total por lotes</span>
        </div>
        <div class="card">
            <h3>Pedidos pendientes</h3>
            <div class="value">{{ $stats['pending_orders'] ?? 0 }}</div>
            <span class="chip text-yellow-200"><i class="ri-timer-2-line"></i> Por preparar</span>
        </div>
        <div class="card">
            <h3>Traspasos hoy</h3>
            <div class="value">{{ $stats['transfers_today'] ?? 0 }}</div>
            <span class="chip text-green-300"><i class="ri-shuffle-line"></i> Registrados</span>
        </div>
        <div class="card">
            <h3>Alertas de caducidad</h3>
            <div class="value">{{ $stats['expiring_lots'] ?? 0 }} lotes</div>
            <span class="chip text-red-300"><i class="ri-error-warning-line"></i> Vencen en 30 días</span>
        </div>
    </div>

    <div class="dashboard-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px,1fr)); gap:1rem; margin-top:1rem;">
        <div class="card">
            <div class="chart-head">
                <h4>Capacidad por almacén</h4>
                <span class="chip text-white/70">Carga vs capacidad</span>
            </div>
            <canvas id="capacityChart" height="180"></canvas>
        </div>
        <div class="card">
            <div class="chart-head">
                <h4>Entradas vs salidas</h4>
                <span class="chip text-white/70">Últimos 7 días</span>
            </div>
            <canvas id="flowChart" height="180"></canvas>
        </div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div class="chart-head" style="display:flex; justify-content:space-between; align-items:center;">
            <h4>Últimos traspasos</h4>
            <a href="{{ route('dashboard.almacen.transfers') }}" class="pill-button ghost">Ver todos</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Estado</th>
                        <th>Fecha estimada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransfers ?? [] as $transfer)
                        <tr>
                            <td>#{{ $transfer->id }}</td>
                            <td>{{ $transfer->fromWarehouse->name ?? 'Sin origen' }}</td>
                            <td>{{ $transfer->toWarehouse->name ?? 'Sin destino' }}</td>
                            <td>
                                <span class="chip text-white/70">{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</span>
                            </td>
                            <td>{{ optional($transfer->expected_date)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                            <td>
                                <button class="pill-button ghost" onclick="location.href='{{ route('dashboard.almacen.transfers') }}'">Ver</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const capacityLabels = @json($capacityChart['labels'] ?? []);
    const capacityData = @json($capacityChart['data'] ?? []);
    const transferLabels = @json($transferSeries['labels'] ?? []);
    const transferData = @json($transferSeries['data'] ?? []);

    const capacityCtx = document.getElementById('capacityChart');
    if (capacityCtx) {
        new Chart(capacityCtx, {
            type: 'bar',
            data: {
                labels: capacityLabels,
                datasets: [
                    {
                        label: 'Ocupado',
                        data: capacityData,
                        backgroundColor: 'rgba(78,107,175,0.65)',
                        borderRadius: 10,
                    },
                    {
                        label: 'Capacidad',
                        data: capacityLabels.map(() => 100),
                        backgroundColor: 'rgba(255,255,255,0.15)',
                        borderRadius: 10,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#fff' } }
                },
                scales: {
                    x: { ticks: { color: '#fff' }, grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff', callback: v => v + '%' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });
    }

    const flowCtx = document.getElementById('flowChart');
    if (flowCtx) {
        new Chart(flowCtx, {
            type: 'line',
            data: {
                labels: transferLabels,
                datasets: [
                    {
                        label: 'Traspasos',
                        data: transferData,
                        borderColor: '#4ade80',
                        backgroundColor: 'rgba(74,222,128,0.2)',
                        fill: true,
                        tension: 0.25,
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#fff' } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { ticks: { color: '#fff' }, grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
