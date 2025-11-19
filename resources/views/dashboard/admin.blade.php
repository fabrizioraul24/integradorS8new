@extends('layouts.sidebar')

@section('title', 'Dashboard Administrador | Pil Andina')
@section('page-title', 'Radar Ejecutivo')

@section('content')
    <div class="stats-grid">
        <div class="card">
            <h3>Ventas del dia</h3>
            <div class="value">Bs {{ number_format($kpis['sales_today'], 2) }}</div>
            <span class="chip text-green-300"><i class="ri-arrow-up-line"></i> Actualizado hoy</span>
        </div>
        <div class="card">
            <h3>Clientes registrados</h3>
            <div class="value">{{ $kpis['customers'] }}</div>
            <span class="chip text-white/70"><i class="ri-building-4-line"></i> Empresas + tiendas</span>
        </div>
        <div class="card">
            <h3>Productos activos</h3>
            <div class="value">{{ $kpis['products_active'] }}</div>
            <span class="chip text-white/70"><i class="ri-shopping-bag-line"></i> Catalogo disponible</span>
        </div>
        <div class="card">
            <h3>Traspasos abiertos</h3>
            <div class="value">{{ $kpis['transfers_active'] }}</div>
            <span class="chip"><i class="ri-shuffle-line"></i> Pendientes o en transito</span>
        </div>
    </div>

    <div class="charts-grid">
        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Ventas ultimos 7 dias</h4>
                <span class="chip">Serie diaria</span>
            </div>
            <canvas id="salesChart" style="max-height:260px;"></canvas>
        </div>

        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Mix por categoria</h4>
                <span class="chip">Top categorias</span>
            </div>
            <canvas id="categoryChart" style="max-height:260px;"></canvas>
        </div>

        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Estado de traspasos</h4>
                <span class="chip">Resumen</span>
            </div>
            <canvas id="transferChart" style="max-height:260px;"></canvas>
        </div>

        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Usuarios por rol</h4>
                <span class="chip">Distribucion</span>
            </div>
            <canvas id="roleChart" style="max-height:260px;"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const colors = {
        primary: '#4e6baf',
        primaryLight: '#86acd4',
        accent: '#42568b',
        green: '#4ade80',
        red: '#f87171',
        yellow: '#fbbf24'
    };

    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($salesSeries['labels']),
            datasets: [{
                label: 'Ventas (Bs)',
                data: @json($salesSeries['data']),
                borderColor: colors.primary,
                backgroundColor: 'rgba(78,107,175,0.25)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.07)' }, ticks: { color: 'rgba(255,255,255,0.8)' } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.8)' } }
            }
        }
    });

    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($categoryMix['labels']),
            datasets: [{
                label: 'Productos',
                data: @json($categoryMix['data']),
                backgroundColor: colors.primary,
                borderRadius: 8,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: 'rgba(255,255,255,0.8)' }, grid: { display: false } },
                y: { ticks: { color: 'rgba(255,255,255,0.8)' }, grid: { color: 'rgba(255,255,255,0.05)' }, beginatZero: true }
            }
        }
    });

    const transferCtx = document.getElementById('transferChart').getContext('2d');
    new Chart(transferCtx, {
        type: 'doughnut',
        data: {
            labels: @json($transferStatuses['labels']),
            datasets: [{
                data: @json($transferStatuses['data']),
                backgroundColor: [colors.primary, colors.primaryLight, colors.accent, colors.yellow, colors.red],
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom', labels: { color: 'rgba(255,255,255,0.8)' } } }
        }
    });

    const roleCtx = document.getElementById('roleChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'bar',
        data: {
            labels: @json($roleMix['labels']),
            datasets: [{
                label: 'Usuarios',
                data: @json($roleMix['data']),
                backgroundColor: colors.primaryLight,
                borderRadius: 8,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: 'rgba(255,255,255,0.8)' }, grid: { display: false } },
                y: { ticks: { color: 'rgba(255,255,255,0.8)' }, grid: { color: 'rgba(255,255,255,0.05)' }, beginatZero: true }
            }
        }
    });
</script>
@endpush

