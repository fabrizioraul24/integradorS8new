@extends('layouts.sidebar')

@section('title', 'Backups | Pil Andina')
@section('page-title', 'Historial de backups')

@section('content')
    @if(session('status'))
        <div class="card">
            <span class="chip text-white/90"><i class="ri-check-line"></i> {{ session('status') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="card" style="border:1px solid rgba(248,113,113,0.4);">
            <span class="chip" style="background:rgba(248,113,113,0.2); color:#fee2e2;"><i class="ri-error-warning-line"></i> {{ session('error') }}</span>
        </div>
    @endif

    <div class="stats-grid">
        <div class="card">
            <h3>Total respaldos</h3>
            <div class="value">{{ $stats['total'] }}</div>
            <span class="chip text-white/70"><i class="ri-database-2-line"></i> Historial</span>
        </div>
        <div class="card">
            <h3>Completados</h3>
            <div class="value">{{ $stats['completed'] }}</div>
            <span class="chip text-green-300"><i class="ri-check-double-line"></i> Ok</span>
        </div>
        <div class="card">
            <h3>Fallidos</h3>
            <div class="value">{{ $stats['failed'] }}</div>
            <span class="chip text-red-300"><i class="ri-close-line"></i> Revisar</span>
        </div>
        <div class="card">
            <h3>Último backup</h3>
            <div class="value">
                {{ optional($stats['last'])->created_at?->format('d/m H:i') ?? 'Sin registros' }}
            </div>
            <span class="chip text-white/70"><i class="ri-time-line"></i> Fecha/hora</span>
        </div>
    </div>

    <div class="card">
        <div class="chart-head" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h4>Generar nuevo backup</h4>
                <p style="margin:0;color:rgba(255,255,255,0.6); font-size:0.9rem;">Ejecuta un respaldo completo de la base de datos (MySQL/MariaDB).</p>
            </div>
            <form method="POST" action="{{ route('dashboard.backups.store') }}">
                @csrf
                <button type="submit" class="pill-button">
                    <i class="ri-download-cloud-2-line"></i> Crear backup
                </button>
            </form>
        </div>
        <p style="color:rgba(255,255,255,0.6); margin-bottom:1rem;">
            Los archivos se almacenan en <code>storage/app/backups</code>. Asegúrate de copiar los archivos a un almacenamiento externo para proteger tu información.
        </p>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Peso</th>
                        <th>Estado</th>
                        <th>Creado por</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr>
                            <td>
                                <strong>{{ $backup->file_name }}</strong>
                                <p style="margin:0; color:rgba(255,255,255,0.6);">{{ strtoupper($backup->disk) }}</p>
                                @if($backup->message)
                                    <small style="color:rgba(255,255,255,0.7);">{{ $backup->message }}</small>
                                @endif
                            </td>
                            <td>{{ $backup->readable_size }}</td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'completed' => ['label' => 'Completado', 'class' => 'text-green-300'],
                                        'running' => ['label' => 'En proceso', 'class' => 'text-yellow-300'],
                                        'failed' => ['label' => 'Fallido', 'class' => 'text-red-300'],
                                    ];
                                    $label = $statusLabels[$backup->status] ?? ['label' => ucfirst($backup->status), 'class' => ''];
                                @endphp
                                <span class="chip {{ $label['class'] }}">{{ $label['label'] }}</span>
                            </td>
                            <td>{{ $backup->creator->name ?? 'Sistema' }}</td>
                            <td>{{ optional($backup->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('dashboard.backups.download', $backup) }}" class="pill-button ghost" @if($backup->status !== 'completed') style="pointer-events:none; opacity:0.4;" @endif>
                                        <i class="ri-download-2-line"></i> Descargar
                                    </a>
                                    <form method="POST" action="{{ route('dashboard.backups.destroy', $backup) }}" onsubmit="return confirm('¿Eliminar este backup del historial?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:1.5rem;">Aún no generaste backups.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">
            {{ $backups->links() }}
        </div>
    </div>
@endsection
