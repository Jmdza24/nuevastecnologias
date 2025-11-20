@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h2 class="fw-bold"> Gestión de Usuarios</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
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
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <td>
                            @if($user->role === 'admin')
                                <span class="badge bg-danger">Administrador</span>
                            @elseif($user->role === 'tecnico')
                                <span class="badge bg-info text-dark">Técnico</span>
                            @else
                                <span class="badge bg-secondary">Cliente</span>
                            @endif
                        </td>

                        <td>
                            @if($user->active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-dark">Inactivo</span>
                            @endif
                        </td>

                        <td class="text-end">

                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>

                            @if($user->role !== 'admin')
                                @if($user->active)
                                    <form action="{{ route('admin.users.disable', $user) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm btn-dark">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.activate', $user) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm btn-success">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
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
