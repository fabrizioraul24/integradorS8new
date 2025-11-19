@extends('layouts.sidebar')

@section('title', 'Logs del sistema | Pil Andina')
@section('page-title', 'Bitacora de acciones')

@section('content')
    <div class="card">
        <div class="chip" style="margin-bottom:1rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
            @php $scope = $filters['scope'] ?? 'all'; @endphp
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'all'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'all' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Todos
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'login'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'login' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Login/Logout
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'register'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'register' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Registro
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'users'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'users' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Usuarios
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'customers'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'customers' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Clientes
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'products'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'products' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Productos
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'categories'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'categories' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Categorias
            </a>
            <a href="{{ route('dashboard.logs', array_merge(request()->except('page'), ['scope' => 'transfers'])) }}"
               class="btn-secondary" style="text-decoration:none; padding:0.4rem 0.9rem; border-radius:999px; {{ $scope === 'transfers' ? 'background: rgba(255,255,255,0.12);' : '' }}">
                Traspasos
            </a>
        </div>
        <div class="chart-head">
            <h4>Filtros</h4>
        </div>
        <form method="GET" action="{{ route('dashboard.logs') }}" class="form-grid">
            <div class="form-group">
                <label for="actor_id">Usuario (actor)</label>
                <select id="actor_id" name="actor_id" class="select-light">
                    <option value="">Todos</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" @selected($filters['actor_id'] == $actor->id)>{{ $actor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="entity_type">Entidad</label>
                <select id="entity_type" name="entity_type" class="select-light">
                    <option value="">Todas</option>
                    @foreach($entityTypes as $type)
                        <option value="{{ $type }}" @selected($filters['entity_type'] === $type)>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="action">Accion</label>
                <select id="action" name="action" class="select-light">
                    <option value="">Todas</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected($filters['action'] === $action)>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="align-self:flex-end;">
                <button type="submit" class="pill-button">Aplicar</button>
                <a href="{{ route('dashboard.logs') }}" class="clean-link">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="chart-head">
            <h4>Listado de logs</h4>
            <span class="chip">{{ $logs->total() }} registros</span>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Actor</th>
                    <th>Entidad</th>
                    <th>Accion</th>
                    <th>Descripcion</th>
                    <th>Detalle</th>
                </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ optional($log->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? 'Sistema' }}</td>
                        <td>{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</td>
                        <td><span class="status-pill">{{ ucfirst($log->action) }}</span></td>
                        <td>{{ $log->description ?? '-' }}</td>
                        <td>
                            @if($log->old_values || $log->new_values)
                                @php
                                    $pdfUrl = class_basename($log->entity_type) === 'Transfer'
                                        ? route('dashboard.transfers.report.single', $log->entity_id)
                                        : null;
                                @endphp
                                <button type="button" class="btn-secondary btn-log-detail"
                                        data-old='@json($log->old_values)'
                                        data-new='@json($log->new_values)'
                                        data-entity="{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}"
                                        @if($pdfUrl) data-pdf="{{ $pdfUrl }}" @endif
                                        style="padding:0.35rem 0.75rem;">
                                    Ver
                                </button>
                            @else
                                <span class="text-white/60">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:1.2rem;">Sin registros para los filtros.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $logs->links() }}
        </div>
    </div>

    <div class="modal" id="logDetailModal">
        <div class="modal-content" style="max-width:800px;">
            <div class="modal-header">
                <h3>Detalle de cambio</h3>
                <button class="close-button" type="button" id="closeLogDetail">&times;</button>
            </div>
            <div id="logDetailBody" style="display:grid; gap:1rem;"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const logModal = document.getElementById('logDetailModal');
    const logBody = document.getElementById('logDetailBody');
    const closeLogDetail = document.getElementById('closeLogDetail');

    function renderDiff(oldData, newData) {
        const wrapper = document.createElement('div');
        wrapper.style.display = 'grid';
        wrapper.style.gap = '0.6rem';
        wrapper.style.gridTemplateColumns = 'repeat(auto-fit, minmax(220px, 1fr))';
        const keys = new Set([...Object.keys(oldData || {}), ...Object.keys(newData || {})]);
        keys.forEach((key) => {
            const oldVal = oldData ? oldData[key] : undefined;
            const newVal = newData ? newData[key] : undefined;
            const card = document.createElement('div');
            card.style.border = '1px solid rgba(255,255,255,0.12)';
            card.style.borderRadius = '1rem';
            card.style.padding = '0.75rem 1rem';
            card.innerHTML = `
                <p style="margin:0; font-size:0.85rem; color:rgba(255,255,255,0.7);">${key}</p>
                <p style="margin:0.2rem 0 0;"><strong>Antes:</strong> ${oldVal ?? '-'}</p>
                <p style="margin:0.1rem 0 0;"><strong>Despues:</strong> ${newVal ?? '-'}</p>
            `;
            wrapper.appendChild(card);
        });
        return wrapper;
    }

    document.querySelectorAll('.btn-log-detail').forEach((btn) => {
        btn.addEventListener('click', () => {
            const oldData = JSON.parse(btn.dataset.old || 'null');
            const newData = JSON.parse(btn.dataset.new || 'null');
            const entity = btn.dataset.entity || '';
            const pdfUrl = btn.dataset.pdf || '';
            logBody.innerHTML = '';
            const title = document.createElement('p');
            title.style.margin = '0';
            title.style.color = 'rgba(255,255,255,0.8)';
            title.innerHTML = `<strong>Entidad:</strong> ${entity}`;
            logBody.appendChild(title);
            if (pdfUrl) {
                const link = document.createElement('a');
                link.href = pdfUrl;
                link.target = '_blank';
                link.rel = 'noopener';
                link.textContent = 'Abrir PDF del traspaso';
                link.className = 'pill-button';
                link.style.display = 'inline-flex';
                link.style.width = 'fit-content';
                logBody.appendChild(link);
            }
            logBody.appendChild(renderDiff(oldData, newData));
            logModal.classList.add('active');
        });
    });

    function closeModal() {
        logModal.classList.remove('active');
    }

    closeLogDetail?.addEventListener('click', closeModal);
    window.addEventListener('click', (event) => {
        if (event.target === logModal) closeModal();
    });
</script>
@endpush

