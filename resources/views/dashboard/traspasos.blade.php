@extends('layouts.sidebar')

@section('title', 'Traspasos internos | Pil Andina')
@section('page-title', 'Traspasos de productos')

@section('content')
    @php
        $statusLabels = [
            \App\Models\Transfer::STATUS_PENDING => 'Pendiente',
            \App\Models\Transfer::STATUS_IN_TRANSIT => 'En transito',
            \App\Models\Transfer::STATUS_RECEIVED => 'Recibido',
        ];
    @endphp

    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90">{{ session('status') }}</span>
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Traspasos registrados</h3>
            <div class="value">{{ $stats['total'] }}</div>
            <span class="chip text-white/70"><i class="ri-stack-line"></i>Total historico</span>
        </div>
        <div class="card">
            <h3>Pendientes</h3>
            <div class="value">{{ $stats['pending'] }}</div>
            <span class="chip text-yellow-300"><i class="ri-time-line"></i>Por atender</span>
        </div>
        <div class="card">
            <h3>En transito</h3>
            <div class="value">{{ $stats['in_transit'] }}</div>
            <span class="chip text-blue-300"><i class="ri-truck-line"></i>Moviendose</span>
        </div>
        <div class="card">
            <h3>Recibidos</h3>
            <div class="value">{{ $stats['received'] }}</div>
            <span class="chip text-green-300"><i class="ri-checkbox-circle-line"></i>Confirmados</span>
        </div>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Reportes ejecutivos</h4>
            <a class="pill-button" target="_blank" rel="noopener" href="{{ route('dashboard.transfers.report') }}">
                <i class="ri-file-pdf-line mr-1"></i> Generar reporte PDF
            </a>
        </div>
        <p class="text-white/70">Descarga un resumen profesional listo para compartir con los responsables logisticos.</p>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Nuevo traspaso</h4>
        </div>
        <form method="POST" action="{{ route('dashboard.transfers.store') }}" id="transferForm">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="from_warehouse_id">AlmacAn origen (opcional)</label>
                    <select id="from_warehouse_id" name="from_warehouse_id" class="select-light">
                        <option value="">Seleccionar</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(old('from_warehouse_id') == $warehouse->id)>{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                        @endforeach
                    </select>
                    @error('from_warehouse_id')<small style="color:#f87171">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="to_warehouse_id">AlmacAn destino</label>
                    <select id="to_warehouse_id" name="to_warehouse_id" class="select-light" required>
                        <option value="">Seleccionar</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(old('to_warehouse_id') == $warehouse->id)>{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                        @endforeach
                    </select>
                    @error('to_warehouse_id')<small style="color:#f87171">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="expected_date">Fecha estimada</label>
                    <input type="date" id="expected_date" name="expected_date" class="input-ghost" value="{{ old('expected_date') }}">
                    @error('expected_date')<small style="color:#f87171">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label for="status">Estado inicial</label>
                    <select id="status" name="status" class="select-light">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', \App\Models\Transfer::STATUS_PENDING) === $status)>{{ $statusLabels[$status] }}</option>
                        @endforeach
                    </select>
                    @error('status')<small style="color:#f87171">{{ $message }}</small>@enderror
                </div>
                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="notes">Notas generales</label>
                    <textarea id="notes" name="notes" class="input-ghost" rows="2" placeholder="Instrucciones para logistica">{{ old('notes') }}</textarea>
                    @error('notes')<small style="color:#f87171">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="transfer-items-wrapper">
                <div class="chart-head" style="margin-top:1.2rem;">
                    <h4>Productos a traspasar</h4>
                    <button type="button" class="pill-button" id="addTransferItem" data-lookup-url="{{ route('dashboard.transfers.lookup') }}">
                        <i class="ri-add-line"></i>Agregar producto
                    </button>
                </div>
                <p class="text-white/70" style="margin-bottom:1rem;">Introduce el codigo (SKU) para rellenar automaticamente los datos y la cantidad disponible en el almacen de origen.</p>
                <div id="transferItems"></div>
                @error('items')<small style="color:#f87171">{{ $message }}</small>@enderror
                @error('items.*.product_id')<small style="color:#f87171">{{ $message }}</small>@enderror
            </div>

            <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
                <button type="submit" class="pill-button">Guardar traspaso</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Traspasos recientes</h4>
            <span class="chip">{{ $transfers->total() }} registros</span>
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
                        <th>Productos</th>
                        <th>Solicitado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        @php
                            $statusSlug = \Illuminate\Support\Str::slug($transfer->status, '_');
                        @endphp
                        <tr>
                            <td>#{{ $transfer->id }}</td>
                            <td>{{ $transfer->fromWarehouse->name ?? 'No definido' }}</td>
                            <td>{{ $transfer->toWarehouse->name ?? 'N/A' }}</td>
                            <td>
                                <span class="status-pill {{ $statusSlug }}">
                                    {{ $statusLabels[$transfer->status] ?? ucfirst($transfer->status) }}
                                </span>
                            </td>
                            <td>{{ optional($transfer->expected_date)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                            <td>
                                <a class="btn-secondary" target="_blank" rel="noopener"
                                    href="{{ route('dashboard.transfers.report.single', $transfer) }}">
                                    Generar reporte
                                </a>
                            </td>
                            <td>{{ $transfer->requestedByUser->name ?? 'Usuario Pil' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:1rem;">Sin traspasos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $transfers->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function() {
        const itemsContainer = document.getElementById('transferItems');
        const addItemButton = document.getElementById('addTransferItem');
        const lookupUrl = addItemButton.dataset.lookupUrl;
        const fromWarehouseSelect = document.getElementById('from_warehouse_id');
        let itemIndex = 0;

        function createRow(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'transfer-item-row';
            wrapper.dataset.index = index;
            wrapper.innerHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label>cadigo (SKU)</label>
                        <input type="text" class="input-ghost sku-input" placeholder="Ej. 133" data-index="${index}">
                        <input type="hidden" name="items[${index}][product_id]" class="product-id-input" required>
                    </div>
                    <div class="form-group">
                        <label>Producto</label>
                        <input type="text" class="input-ghost product-name" placeholder="BuscA por cadigo" readonly>
                    </div>
                    <div class="form-group">
                        <label>Disponible en origen</label>
                        <input type="text" class="input-ghost available-qty" placeholder="0" readonly>
                    </div>
                    <div class="form-group">
                        <label>cantidad solicitada</label>
                        <input type="number" min="1" class="input-ghost qty-input" name="items[${index}][requested_qty]" required>
                    </div>
                    <div class="form-group" style="grid-column:1 / -1;">
                        <label>Notas</label>
                        <textarea class="input-ghost note-input" name="items[${index}][notes]" rows="1" placeholder="Para diferencias, lotes, etc."></textarea>
                    </div>
                </div>
                <button type="button" class="btn-danger remove-item">Quitar</button>
            `;
            return wrapper;
        }

        function addItemRow() {
            const row = createRow(itemIndex++);
            itemsContainer.appendChild(row);
        }

        function handleLookup(row, sku) {
            const productInput = row.querySelector('.product-id-input');
            const nameInput = row.querySelector('.product-name');
            const qtyavailable = row.querySelector('.available-qty');
            const qtyInput = row.querySelector('.qty-input');

            productInput.value = '';
            nameInput.value = 'Buscando...';
            qtyavailable.value = '';

            const params = new URLSearchParams({ sku });
            if (fromWarehouseSelect.value) {
                params.append('warehouse_id', fromWarehouseSelect.value);
            }

            fetch(`${lookupUrl}?${params.toString()}`)
                .then((response) => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then((data) => {
                    productInput.value = data.product_id;
                    nameInput.value = `${data.name} (${data.sku})`;
                    qtyavailable.value = (data.available_quantity ?? 0) + ' uds';
                    if (!qtyInput.value) {
                        qtyInput.value = data.available_quantity && data.available_quantity > 0
                            ? data.available_quantity
                            : 1;
                    }
                })
                .catch(async (errorResponse) => {
                    let message = 'No pudimos encontrar el producto.';
                    if (errorResponse.json) {
                        const data = await errorResponse.json();
                        if (data?.message) message = data.message;
                    }
                    nameInput.value = message;
                    qtyavailable.value = '0';
                    productInput.value = '';
                    qtyInput.value = '';
                });
        }

        addItemButton.addEventListener('click', addItemRow);

        itemsContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-item')) {
                event.target.closest('.transfer-item-row')?.remove();
            }
        });

        itemsContainer.addEventListener('blur', (event) => {
            if (event.target.classList.contains('sku-input')) {
                const sku = event.target.value.trim();
                if (!sku) return;
                const row = event.target.closest('.transfer-item-row');
                handleLookup(row, sku);
            }
        }, true);

        addItemRow();
    })();

    // Modal de productos eliminado: cada traspaso ahora genera un PDF individual desde la tabla.
</script>
@endpush


