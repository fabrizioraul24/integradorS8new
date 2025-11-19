@extends('layouts.sidebar-almacen')

@section('title', 'Registro de daños | Pil Andina')
@section('page-title', 'Registro de daños')

@section('content')
    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90">{{ session('status') }}</span>
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Reportes</h3>
            <div class="value">{{ $stats['reports'] }}</div>
            <span class="chip text-white/70"><i class="ri-flag-2-fill"></i>Total registrados</span>
        </div>
        <div class="card">
            <h3>Unidades afectadas</h3>
            <div class="value">{{ number_format($stats['units']) }}</div>
            <span class="chip text-red-300"><i class="ri-close-circle-line"></i>Retiradas de stock</span>
        </div>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Registrar daño</h4>
        </div>
        <form method="POST" action="{{ route('dashboard.almacen.damages.store') }}" class="form-grid">
            @csrf
            <input type="hidden" name="product_lot_id" id="product_lot_id" value="{{ old('product_lot_id') }}">
            <div class="form-group" style="grid-column: span 2;">
                <label for="damage_lot_input">Código del lote</label>
                <input type="text" id="damage_lot_input" class="input-ghost" placeholder="Ej. LPZ-2025-A001" autocomplete="off">
                <small>Escribe el código del lote y se llenará la información automáticamente.</small>
                @error('product_lot_id')<small style="color:#f87171">{{ $message }}</small>@enderror
            </div>
            <div class="form-group">
                <label>Producto</label>
                <input type="text" id="damage_product" class="input-ghost" readonly>
            </div>
            <div class="form-group">
                <label>SKU</label>
                <input type="text" id="damage_sku" class="input-ghost" readonly>
            </div>
            <div class="form-group">
                <label>Lote</label>
                <input type="text" id="damage_lot_code" class="input-ghost" readonly>
            </div>
            <div class="form-group">
                <label>Disponible</label>
                <input type="text" id="damage_stock" class="input-ghost" readonly>
            </div>
            <div class="form-group">
                <label>Vence</label>
                <input type="text" id="damage_exp" class="input-ghost" readonly>
            </div>
            <div class="form-group">
                <label for="damaged_qty">Cantidad dañada</label>
                <input type="number" min="1" id="damaged_qty" name="damaged_qty" class="input-ghost" value="{{ old('damaged_qty') }}" required>
                @error('damaged_qty')<small style="color:#f87171">{{ $message }}</small>@enderror
            </div>
            <div class="form-group" style="grid-column:1 / -1;">
                <label for="comment">Comentario</label>
                <textarea id="comment" name="comment" rows="3" class="input-ghost" placeholder="Describe el daño o la incidencia">{{ old('comment') }}</textarea>
            </div>
            <div style="grid-column:1 / -1; display:flex; justify-content:flex-end;">
                <button class="pill-button" type="submit">Registrar daño</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Historial de daños</h4>
            <span class="chip text-white/70">{{ $reports->total() }} registros</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Almacén</th>
                        <th>Comentado por</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->lot->lote_code ?? 'Sin código' }}</td>
                            <td>
                                <strong>{{ $report->product->name ?? 'Producto' }}</strong>
                                <p style="margin:0;color:rgba(255,255,255,0.6);">SKU: {{ $report->product->sku ?? 'N/D' }}</p>
                            </td>
                            <td>{{ $report->damaged_qty }} uds</td>
                            <td>{{ $report->warehouse->name ?? 'Almacén' }}</td>
                            <td>{{ $report->reporter->name ?? 'Sistema' }}</td>
                            <td>{{ optional($report->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:1.5rem;">Sin incidencias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        const lotInput = document.getElementById('damage_lot_input');
        const lotIdField = document.getElementById('product_lot_id');
        const productField = document.getElementById('damage_product');
        const skuField = document.getElementById('damage_sku');
        const stockField = document.getElementById('damage_stock');
        const expField = document.getElementById('damage_exp');
        const lotCodeField = document.getElementById('damage_lot_code');
        const lookupUrl = "{{ route('dashboard.almacen.damages.lookup') }}";
        let lookupTimeout = null;

        function fillFields(data) {
            lotIdField.value = data.lot_id || '';
            productField.value = data.product || '';
            skuField.value = data.sku || '';
            stockField.value = data.quantity ? `${data.quantity} uds` : '';
            expField.value = data.expires_at ? new Date(data.expires_at).toLocaleDateString() : '';
            lotCodeField.value = data.lot_code || '';
        }

        function lookupLot() {
            const code = lotInput?.value?.trim();
            if (!code) {
                fillFields({});
                return;
            }
            fetch(`${lookupUrl}?code=${encodeURIComponent(code)}`)
                .then(response => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then(fillFields)
                .catch(async (error) => {
                    fillFields({});
                    let message = 'No encontramos un lote con ese código.';
                    if (error.json) {
                        const payload = await error.json();
                        if (payload?.message) message = payload.message;
                    }
                    console.warn(message);
                });
        }

        lotInput?.addEventListener('input', () => {
            clearTimeout(lookupTimeout);
            lookupTimeout = setTimeout(lookupLot, 500);
        });
    })();
</script>
@endpush
