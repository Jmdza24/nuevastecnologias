@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-4">📊 Panel de Administración</h2>

    <div class="row g-3">

        <x-dashboard-card title="Total Tickets" :value="$total" color="primary" />
        <x-dashboard-card title="Abiertos" :value="$open" color="success" />
        <x-dashboard-card title="En proceso" :value="$in_progress" color="info" />
        <x-dashboard-card title="Esperando cliente" :value="$waiting_client" color="warning" />
        <x-dashboard-card title="Terminados" :value="$finished" color="secondary" />
        <x-dashboard-card title="Cerrados" :value="$closed" color="dark" />

    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">📝 Últimos Tickets</h4>
        <a href="{{ route('tickets.index') }}" class="btn btn-primary">
            Ver todos los tickets
        </a>
    </div>

    <table class="table table-bordered mt-3">
        <thead class="table-secondary">
            <tr>
                <th>ID</th>
                <th>Asunto</th>
                <th>Cliente</th>
                <th>Técnico</th>
                <th>Estado</th>
                <th>Creado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->subject }}</td>
                    <td>{{ $t->creator->name }}</td>
                    <td>{{ $t->assignedTo->name ?? 'Sin asignar' }}</td>

                    <td>
                        <span class="badge bg-info text-dark">{{ $t->status }}</span>
                    </td>

                    <td>{{ $t->created_at->format('d/m/Y') }}</td>

                    <td class="text-end">
                        <a href="{{ route('tickets.show', $t) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('tickets.edit', $t) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="my-4">

    <h4 class="fw-bold mb-3">🧑‍🔧 Tickets por técnico</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Técnico</th>
                <th>Email</th>
                <th>Tickets asignados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticketsPorTecnico as $tec)
                <tr>
                    <td>{{ $tec->name }}</td>
                    <td>{{ $tec->email }}</td>
                    <td>{{ $tec->tickets_assigned_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">👥 Usuarios del sistema</h4>

        <a href="{{ route('admin.users.index') }}" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Ver todos los usuarios
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $u->role === 'admin' ? 'danger' : 
                                    ($u->role === 'tecnico' ? 'info text-dark' : 'secondary')
                                }}">
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-{{ $u->active ? 'success' : 'dark' }}">
                                    {{ $u->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <td class="text-end">

                                <a class="btn btn-warning btn-sm" href="{{ route('admin.users.edit', $u) }}">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                @if($u->role !== 'admin')
                                    @if($u->active)
                                        <form action="{{ route('admin.users.disable', $u) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button class="btn btn-dark btn-sm">
                                                <i class="bi bi-person-dash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.users.activate', $u) }}" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button class="btn btn-success btn-sm">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
