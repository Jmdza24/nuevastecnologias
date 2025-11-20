@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tickets</h2>

        @if($user->role === 'cliente')
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                Crear Ticket
            </a>
        @endif
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Asunto</th>

                        @if($user->role !== 'cliente')
                            <th>Cliente</th>
                        @endif

                        <th>Estado</th>
                        <th>Técnico</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>

                            <td>{{ $ticket->subject }}</td>

                            @if($user->role !== 'cliente')
                                <td>{{ $ticket->creator->name }}</td>
                            @endif

                            <td>
                                @php
                                    $colors = [
                                        'open' => 'primary',
                                        'in_progress' => 'warning',
                                        'waiting_client' => 'info',
                                        'finished' => 'success',
                                        'closed' => 'secondary'
                                    ];
                                @endphp

                                <span class="badge bg-{{ $colors[$ticket->status] ?? 'dark' }}">
                                    {{ $ticket->status }}
                                </span>
                            </td>

                            <td>{{ $ticket->technician->name ?? 'Sin asignar' }}</td>

                            <td>{{ $ticket->created_at->format('d/m/Y') }}</td>

                            <td>
                                <!-- Ver -->
                                <a href="{{ route('tickets.show', $ticket) }}" 
                                   class="btn btn-sm btn-info">
                                    Ver
                                </a>

                                <!-- Editar para técnicos y admin -->
                                @if($user->role === 'tecnico' || $user->role === 'admin')
                                    <a href="{{ route('tickets.edit', $ticket) }}" 
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                @endif

                                <!-- Cerrar para cliente -->
                                @if($user->role === 'cliente' && $ticket->status !== 'closed')
                                    <form action="{{ route('tickets.close', $ticket) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success">
                                            Cerrar
                                        </button>
                                    </form>
                                @endif

                                <!-- Eliminar solo admin -->
                                @if($user->role === 'admin')
                                    <form action="{{ route('tickets.destroy', $ticket) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Eliminar ticket?')">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-3">
                                No hay tickets para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection
