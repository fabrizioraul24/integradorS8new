@extends('layouts.sidebar-almacen')

@section('title', 'Inventario por lotes | Pil Andina')
@section('page-title', 'Inventario por lotes')

@section('content')
    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90">{{ session('status') }}</span>
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Lotes registrados</h3>
            <div class="value">{{ $stats['lots'] }}</div>
            <span class="chip text-white/70"><i class="ri-barcode-line"></i>Total activos</span>
        </div>
        <div class="card">
            <h3>Stock total</h3>
            <div class="value">{{ number_format($stats['stock']) }} uds</div>
            <span class="chip text-white/70"><i class="ri-dropbox-line"></i>Inventario físico</span>
        </div>
        <div class="card">
            <h3>Vencen en 30 días</h3>
            <div class="value">{{ $stats['expiring'] }}</div>
            <span class="chip text-red-300"><i class="ri-error-warning-line"></i>Priorizar</span>
        </div>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Filtrar lotes</h4>
        </div>
        <form method="GET" action="{{ route('dashboard.almacen.lots') }}" class="form-grid">
            <div class="form-group">
                <label for="product_id">Producto</label>
                <select id="product_id" name="product_id" class="select-light">
                    <option value="">Todos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(($filters['product_id'] ?? null) == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="warehouse_id">Almacén</label>
                <select id="warehouse_id" name="warehouse_id" class="select-light">
                    <option value="">Todos</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" @selected(($filters['warehouse_id'] ?? null) == $warehouse->id)>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="expires_at">Fecha de vencimiento</label>
                <input type="date" id="expires_at" name="expires_at" class="input-ghost" value="{{ $filters['expires_at'] ?? '' }}">
            </div>
            <div class="form-group" style="align-self:flex-end;">
                <button class="pill-button" type="submit">Aplicar</button>
                <a href="{{ route('dashboard.almacen.lots') }}" class="clean-link">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Inventario detallado</h4>
            <span class="chip text-white/70">{{ $lots->total() }} registros</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Lote</th>
                        <th>Almacén</th>
                        <th>Cantidad</th>
                        <th>Expira</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lots as $lot)
                        @php
                            $expiresSoon = $lot->expires_at && $lot->expires_at->lt(now()->addDays(30));
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $lot->product->name ?? 'Producto' }}</strong>
                                <p style="margin:0;color:rgba(255,255,255,0.6);">SKU: {{ $lot->product->sku ?? 'N/D' }}</p>
                            </td>
                            <td>{{ $lot->lote_code ?? 'Sin código' }}</td>
                            <td>{{ $lot->warehouse->name ?? 'Almacén' }}</td>
                            <td>{{ number_format($lot->quantity) }} uds</td>
                            <td>{{ optional($lot->expires_at)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>
                                <span class="chip {{ $expiresSoon ? 'text-red-300' : 'text-green-300' }}">
                                    {{ $expiresSoon ? 'Prioritario' : 'Normal' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:1.5rem;">No encontramos lotes con esos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $lots->links() }}
        </div>
    </div>
@endsection
