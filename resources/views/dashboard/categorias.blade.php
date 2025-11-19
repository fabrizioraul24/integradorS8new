@extends('layouts.sidebar')

@section('title', 'Categorias de Productos | Pil Andina')
@section('page-title', 'Gestion de Categorias')

@section('content')
    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90">{{ session('status') }}</span>
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Total categorias</h3>
            <div class="value">{{ $total }}</div>
            <span class="chip"><i class="ri-price-tag-3-line"></i> Activas + desactivadas</span>
        </div>
        <div class="card">
            <h3>Con productos asignados</h3>
            <div class="value">{{ $withProducts }}</div>
            <span class="chip text-white/70"><i class="ri-check-line"></i> Operativas</span>
        </div>
        <div class="card">
            <h3>Desactivadas</h3>
            <div class="value">{{ $inactive }}</div>
            <span class="chip text-white/70"><i class="ri-pause-line"></i> En pausa</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3>Filtro</h3>
                <p class="text-muted">Encuentra categorias por nombre o descripcion.</p>
            </div>
            <form method="GET" action="{{ route('dashboard.categories') }}" class="filters">
                <input type="text" id="search" name="search" class="input-ghost" value="{{ $search }}" placeholder="Lacteos, Bebidas...">
                <button type="submit" class="pill-button">Filtrar</button>
                <a href="{{ route('dashboard.categories') }}" class="pill-button ghost">Limpiar</a>
            </form>
        </div>
    </div>

    <div class="layout-2col">
        <div class="card">
            <h4>Nueva categoria</h4>
            <p class="text-muted">Define una categoria para agrupar productos.</p>
            <form method="POST" action="{{ route('dashboard.categories.store') }}" class="form-grid">
                @csrf
                <div class="form-group">
                    <label for="category_name">Nombre</label>
                    <input type="text" id="category_name" name="name" class="input-ghost" placeholder="Lacteos" required>
                </div>
                <div class="form-group">
                    <label for="category_description">Descripcion</label>
                    <textarea id="category_description" name="description" rows="3" class="input-ghost" placeholder="Productos de origen lacteo"></textarea>
                </div>
                <button type="submit" class="pill-button">Guardar categoria</button>
            </form>
        </div>

        <div>
            <div class="card">
                <h4>Categorias activas</h4>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeCategories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description ?? 'Sin descripcion' }}</td>
                                    <td>{{ $category->products_count }}</td>
                                    <td class="actions">
                                        <button class="pill-button ghost" onclick="openEditModal({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}')">Editar</button>
                                        <form method="POST" action="{{ route('dashboard.categories.destroy', $category) }}" onsubmit="return confirm('Desactivar la categoria {{ $category->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="pill-button danger" type="submit">Desactivar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:1.2rem;">No hay categorias para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h4>Categorias desactivadas</h4>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inactiveCategories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description ?? 'Sin descripcion' }}</td>
                                    <td>{{ $category->products_count }}</td>
                                    <td class="actions">
                                        <form method="POST" action="{{ route('dashboard.categories.restore', $category->id) }}">
                                            @csrf
                                            <button class="pill-button" type="submit">Reactivar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align:center;padding:1.2rem;">No hay categorias desactivadas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="editModal" style="display:none;">
        <div class="modal-content">
            <button class="icon-button close" onclick="closeEditModal()"><i class="ri-close-line"></i></button>
            <h3>Editar categoria</h3>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit_category_name">Nombre</label>
                    <input type="text" id="edit_category_name" name="name" class="input-ghost" required>
                </div>
                <div class="form-group">
                    <label for="edit_category_description">Descripcion</label>
                    <textarea id="edit_category_description" name="description" rows="3" class="input-ghost"></textarea>
                </div>
                <button type="submit" class="pill-button">Actualizar</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, description) {
            document.getElementById('edit_category_name').value = name;
            document.getElementById('edit_category_description').value = description;
            document.getElementById('editForm').action = `/dashboard/categories/${id}`;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
@endsection
