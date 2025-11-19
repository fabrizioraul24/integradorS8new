@extends('layouts.sidebar')

@section('title', 'Agente Inteligente | Pil Andina')
@section('page-title', 'Agente Inteligente')

@section('content')
    <style>
        .chart-card {
            background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(23,34,59,0.85));
            border-radius: 1.2rem;
            padding: 1rem;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), 0 20px 40px rgba(2,6,23,0.35);
            min-height: 280px;
        }
        .chart-shell {
            background: rgba(9,14,30,0.9);
            border-radius: 1rem;
            padding: 0.75rem;
            height: calc(100% - 2rem);
        }
    </style>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h2 style="margin:0; color:#fff;">Resumen del agente inteligente</h2>
        <div style="display:flex; gap:0.5rem;">
            <button class="pill-button ghost" onclick="document.getElementById('chartsSection').scrollIntoView({behavior:'smooth'})">
                <i class="ri-line-chart-line"></i>Ver gráficos
            </button>
            <a href="{{ route('dashboard.agent.report') }}" class="pill-button" target="_blank" rel="noopener">
                <i class="ri-file-text-line"></i>Descargar reporte
            </a>
        </div>
    </div>
    <div class="stats-grid">
        <div class="card">
            <h3>Restock sugeridos</h3>
            <div class="value">{{ $stats['restock'] }}</div>
            <span class="chip text-white/70"><i class="ri-lightbulb-flash-line"></i> Órdenes recomendadas</span>
        </div>
        <div class="card">
            <h3>Alertas de stock</h3>
            <div class="value">{{ $stats['alerts_low'] }}</div>
            <span class="chip text-red-300"><i class="ri-error-warning-line"></i>Bajo inventario</span>
        </div>
        <div class="card">
            <h3>Lotes por vencer</h3>
            <div class="value">{{ $stats['alerts_expiring'] }}</div>
            <span class="chip text-yellow-200"><i class="ri-timer-line"></i>30 días</span>
        </div>
    </div>

    <div class="charts-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:1rem; margin-top:1.5rem;" id="chartsSection">
        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Top productos (pronóstico)</h4>
                <span class="chip">Demanda semanal</span>
            </div>
            <canvas id="forecastChart" style="max-height:260px;"></canvas>
        </div>

        <div class="card chart-placeholder">
            <div class="chart-head">
                <h4>Distribución de restock</h4>
                <span class="chip">Sugerencias</span>
            </div>
            <canvas id="restockChart" style="max-height:260px;"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Predicción de demanda</h4>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Demanda semanal</th>
                        <th>Tendencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['forecast'] ?? [] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['forecast'] }} uds</td>
                            <td>
                                <span class="chip {{ $item['trend'] === 'alza' ? 'text-green-300' : ($item['trend'] === 'baja' ? 'text-red-300' : 'text-white/70') }}">
                                    {{ ucfirst($item['trend']) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-grid" style="display:grid; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); gap:1rem;">
        <div class="card">
            <div class="chart-head">
                <h4>Recomendaciones de restock</h4>
                <span class="chip text-white/70">{{ count($data['restock'] ?? []) }} productos</span>
            </div>
            <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:1rem;">
                @forelse($data['restock'] ?? [] as $item)
                    <li class="summary-card">
                        <strong>{{ $item['name'] }}</strong>
                        <p style="margin:0.3rem 0;">Cantidad sugerida: {{ $item['suggested_qty'] }} uds</p>
                        <small>{{ $item['reason'] }}</small>
                    </li>
                @empty
                    <li>No hay recomendaciones en este momento.</li>
                @endforelse
            </ul>
        </div>
        <div class="card">
            <div class="chart-head">
                <h4>Alertas críticas</h4>
            </div>
            <h5>Stock bajo</h5>
            <ul style="margin:0 0 1rem 0; padding-left:1rem; color:rgba(255,255,255,0.9);">
                @forelse($data['alerts']['low_stock'] ?? [] as $alert)
                    <li>{{ $alert['name'] }} — Stock {{ $alert['stock'] }} vs demanda {{ $alert['forecast'] }}</li>
                @empty
                    <li>Sin alertas.</li>
                @endforelse
            </ul>
            <h5>Vencen pronto</h5>
            <ul style="margin:0; padding-left:1rem; color:rgba(255,255,255,0.9);">
                @forelse($data['alerts']['expiring'] ?? [] as $alert)
                    <li>{{ $alert['name'] }} — {{ $alert['expires_in_days'] }} días ({{ $alert['stock'] }} uds)</li>
                @empty
                    <li>Sin lotes críticos.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const forecastCtx = document.getElementById('forecastChart');
    const restockCtx = document.getElementById('restockChart');
    const forecastLabels = @json($charts['forecast']['labels'] ?? []);
    const forecastData = @json($charts['forecast']['data'] ?? []);
    const restockLabels = @json($charts['restock']['labels'] ?? []);
    const restockData = @json($charts['restock']['data'] ?? []);

    if (forecastCtx && forecastLabels.length) {
        new Chart(forecastCtx, {
            type: 'bar',
            data: {
                labels: forecastLabels,
                datasets: [{
                    label: 'Demanda semanal (uds)',
                    data: forecastData,
                    backgroundColor: 'rgba(78, 107, 175, 0.8)',
                    borderColor: '#4e6baf',
                    borderWidth: 1,
                    borderRadius: 12,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#fff' }, grid: { display: false } },
                    y: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.08)' } }
                }
            }
        });
    }

    if (restockCtx && restockLabels.length) {
        new Chart(restockCtx, {
            type: 'doughnut',
            data: {
                labels: restockLabels,
                datasets: [{
                    data: restockData,
                    backgroundColor: ['#4e6baf', '#86acd4', '#fbbf24', '#f97316', '#ef4444'],
                }]
            },
            options: {
                plugins: {
                    legend: { labels: { color: '#fff' } }
                }
            }
        });
    }
})();
</script>
@endpush
