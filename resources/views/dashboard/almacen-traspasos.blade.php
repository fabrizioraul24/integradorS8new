@extends('layouts.sidebar-almacen')

@section('title', 'Traspasos | Pil Andina')
@section('page-title', 'Recepción de traspasos')

@section('content')
    <style>
        .warehouse-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: rgba(6, 11, 25, 0.65);
            backdrop-filter: blur(6px);
            z-index: 80;
        }
        .warehouse-modal.active {
            display: flex;
        }
        .warehouse-modal .modal-card {
            width: min(960px, 95vw);
            max-height: 90vh;
            background: linear-gradient(140deg, #0f172a, #132347);
            border-radius: 1.5rem;
            color: #fff;
            box-shadow:
                0 25px 60px rgba(2, 6, 23, 0.65),
                inset 0 1px 1px rgba(255,255,255,0.08);
            padding: 1.5rem 1.8rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .warehouse-modal .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding-bottom: 0.75rem;
        }
        .warehouse-modal .modal-body {
            padding-top: 1rem;
            overflow-y: auto;
        }
        .warehouse-modal .modal-footer {
            padding-top: 1rem;
            display: flex;
            justify-content: flex-end;
        }
        .warehouse-modal .summary-card {
            background: rgba(255,255,255,0.05);
        }
        .warehouse-modal .table-wrapper {
            max-height: 320px;
            overflow: auto;
            border-radius: 1rem;
        }
        .warehouse-modal table.data-table th,
        .warehouse-modal table.data-table td {
            line-height: 1.6;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }
    </style>

    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90">{{ session('status') }}</span>
        </div>
    @endif

    <div class="card">
        <div class="chart-head">
            <h4>Filtrar traspasos</h4>
        </div>
        <form method="GET" action="{{ route('dashboard.almacen.transfers') }}" class="form-grid">
            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status" class="select-light">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="align-self:flex-end;">
                <button type="submit" class="pill-button">Aplicar</button>
                <a href="{{ route('dashboard.almacen.transfers') }}" class="clean-link">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Traspasos asignados</h4>
            <span class="chip text-white/70">{{ $transfers->total() }} registros</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Almacenes</th>
                        <th>Estado</th>
                        <th>Fecha requerida</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>#{{ $transfer->id }}</td>
                            <td>
                                <strong>Origen:</strong> {{ $transfer->fromWarehouse->name ?? 'No definido' }}<br>
                                <strong>Destino:</strong> {{ $transfer->toWarehouse->name ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="chip text-white/80">{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</span>
                            </td>
                            <td>{{ optional($transfer->expected_date)->format('d/m/Y') ?? 'Sin fecha' }}</td>
                            <td>
                                <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                                    <button type="button" class="pill-button" data-open-modal="transferModal-{{ $transfer->id }}">Ver</button>
                                    <a href="{{ route('dashboard.transfers.report.single', $transfer) }}" class="pill-button ghost" target="_blank" rel="noopener">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:1.5rem;">Sin traspasos pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $transfers->links() }}
        </div>
    </div>

    @foreach($transfers as $transfer)
        <div class="warehouse-modal" id="transferModal-{{ $transfer->id }}">
            <div class="modal-card">
                <div class="modal-header">
                    <div>
                        <h3>Traspaso #{{ $transfer->id }}</h3>
                        <p style="margin:0;color:rgba(255,255,255,0.7);">
                            {{ $transfer->fromWarehouse->name ?? 'Sin origen' }} → {{ $transfer->toWarehouse->name ?? 'Sin destino' }}
                        </p>
                    </div>
                    <button type="button" class="close-button" data-close-modal>&times;</button>
                </div>
                <div class="modal-body">
                    <div class="summary modal-summary">
                        <div class="summary-card">
                            <strong>Estado</strong>
                            <span>{{ ucfirst(str_replace('_', ' ', $transfer->status)) }}</span>
                        </div>
                        <div class="summary-card">
                            <strong>Fecha estimada</strong>
                            <span>{{ optional($transfer->expected_date)->format('d/m/Y') ?? 'Sin fecha' }}</span>
                        </div>
                        <div class="summary-card">
                            <strong>Items</strong>
                            <span>{{ $transfer->items->count() }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.almacen.transfers.status', $transfer) }}" class="form-grid" style="grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:0.75rem; margin-bottom:1.25rem; align-items:end;">
                        @csrf
                        <div class="form-group">
                            <label for="status-{{ $transfer->id }}">Cambiar estado</label>
                            <select id="status-{{ $transfer->id }}" name="status" class="select-light">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" @selected($transfer->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="align-self:flex-end;">
                            <button type="submit" class="pill-button ghost">Actualizar estado</button>
                        </div>
                    </form>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Solicitado</th>
                                    <th>Recibido</th>
                                    <th>Dañado</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer->items as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'Producto' }}</strong>
                                            <p style="margin:0;color:rgba(255,255,255,0.6);">SKU: {{ $item->product->sku ?? 'N/D' }}</p>
                                        </td>
                                        <td>{{ $item->requested_qty }}</td>
                                        <td>{{ $item->received_qty ?? 0 }}</td>
                                        <td>{{ $item->damaged_qty ?? 0 }}</td>
                                        <td>{{ $item->receiving_note ?? 'Sin comentarios' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard.transfers.report.single', $transfer) }}" target="_blank" rel="noopener" class="pill-button">Descargar PDF</a>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
<script>
    (() => {
        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.openModal);
                target?.classList.add('active');
            });
        });

        document.querySelectorAll('[data-close-modal]').forEach((button) => {
            button.addEventListener('click', () => button.closest('.warehouse-modal')?.classList.remove('active'));
        });

        document.querySelectorAll('.warehouse-modal').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) modal.classList.remove('active');
            });
        });
    })();
</script>
@endpush
