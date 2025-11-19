@extends('layouts.sidebar')

@section('title', 'Lotes | Pil Andina')
@section('page-title', 'Gestion de Lotes (FEFO)')

@section('content')
    @if(session('status'))
        <div class="card"><span class="chip">{{ session('status') }}</span></div>
    @endif

    <div class="card">
        <div class="chart-head">
            <h4>Crear lote</h4>
        </div>
        <form method="POST" action="{{ route('dashboard.lots.store') }}" class="form-grid">
            @csrf
            <div class="form-group">
                <label>Producto</label>
                <select name="product_id" class="select-light" required>
                    <option value="">Seleccionar</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Bodega</label>
                <select name="warehouse_id" class="select-light" required>
                    <option value="">Seleccionar</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Codigo de lote</label>
                <input type="text" name="lote_code" class="input-ghost" placeholder="Opcional">
            </div>
            <div class="form-group">
                <label>Cantidad</label>
                <input type="number" min="1" name="quantity" class="input-ghost" required>
            </div>
            <div class="form-group">
                <label>Fecha expiracion</label>
                <input type="date" name="expires_at" class="input-ghost" required>
            </div>
            <div class="form-group" style="align-self:flex-end;">
                <button type="submit" class="pill-button">Guardar lote</button>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top:1rem;">
        <div class="chart-head">
            <h4>Listado de lotes</h4>
            <span class="chip">{{ $lots->total() }} registros</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Bodega</th>
                        <th>Imagen</th>
                        <th>Lote</th>
                        <th>Cantidad</th>
                        <th>Expira</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lots as $lot)
                        @php
                            $imgPath = $lot->product?->image_path;
                            $imgUrl = $imgPath ? Storage::url($imgPath) : asset('storage/images/logo.png');
                        @endphp
                        <tr>
                            <td>{{ $lot->product->name ?? '-' }}</td>
                            <td>{{ $lot->product->sku ?? '-' }}</td>
                            <td>{{ $lot->warehouse->name ?? '-' }}</td>
                            <td>
                                <img src="{{ $imgUrl }}" alt="{{ $lot->product->name }}" style="width:52px;height:52px;object-fit:cover;border-radius:1rem;border:1px solid rgba(255,255,255,0.1);">
                            </td>
                            <td>{{ $lot->lote_code ?? 'N/A' }}</td>
                            <td>{{ $lot->quantity }}</td>
                            <td>{{ optional($lot->expires_at)->format('d/m/Y') }}</td>
                            <td>
                                <button type="button"
                                        class="pill-button ghost btn-edit-lot"
                                        data-action="{{ route('dashboard.lots.adjust', $lot) }}"
                                        data-lote="{{ $lot->lote_code }}"
                                        data-expires="{{ optional($lot->expires_at)->format('Y-m-d') }}"
                                        data-quantity="{{ $lot->quantity }}">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:1rem;">Sin lotes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $lots->links() }}
        </div>
    </div>

    <div class="modal" id="lotEditModal" style="display:none;">
        <div class="modal-content" style="max-width:520px;">
            <div class="modal-header">
                <h3>Editar lote</h3>
                <button class="close-button" type="button" id="closeLotEdit">&times;</button>
            </div>
            <form method="POST" id="lotEditForm" class="form-grid" style="grid-template-columns:1fr;">
                @csrf
                <div class="form-group">
                    <label>Codigo de lote</label>
                    <input type="text" name="lote_code" id="lot_code_input" class="input-ghost" placeholder="Codigo de lote">
                </div>
                <div class="form-group">
                    <label>Fecha de expiracion</label>
                    <input type="date" name="expires_at" id="lot_expires_input" class="input-ghost" required>
                </div>
                <div class="form-group">
                    <label>Cantidad total del lote</label>
                    <input type="number" name="quantity" id="lot_quantity_input" class="input-ghost" required>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                    <button type="button" class="btn-secondary" id="cancelLotEdit">Cancelar</button>
                    <button type="submit" class="pill-button">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const lotButtons = document.querySelectorAll('.btn-edit-lot');
    const lotModal = document.getElementById('lotEditModal');
    const lotForm = document.getElementById('lotEditForm');
    const lotCodeInput = document.getElementById('lot_code_input');
    const lotExpiresInput = document.getElementById('lot_expires_input');
    const lotQuantityInput = document.getElementById('lot_quantity_input');
    const closeLotEdit = document.getElementById('closeLotEdit');
    const cancelLotEdit = document.getElementById('cancelLotEdit');

    lotButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            lotForm.action = btn.dataset.action;
            lotCodeInput.value = btn.dataset.lote || '';
            lotExpiresInput.value = btn.dataset.expires || '';
            lotQuantityInput.value = btn.dataset.quantity || 0;
            lotModal.style.display = 'flex';
        });
    });

    function closeLotModal() {
        lotModal.style.display = 'none';
    }
    closeLotEdit?.addEventListener('click', closeLotModal);
    cancelLotEdit?.addEventListener('click', closeLotModal);
    lotModal?.addEventListener('click', (e) => {
        if (e.target === lotModal) closeLotModal();
    });
</script>
@endpush
