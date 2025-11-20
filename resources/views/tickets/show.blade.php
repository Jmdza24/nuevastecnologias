@extends('layouts.app')

@section('title', 'Detalle del Ticket')

@section('content')
<div class="container mt-5">

    <a href="{{ route('tickets.index') }}" class="btn btn-secondary mb-3">Volver</a>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Ticket #{{ $ticket->id }} - {{ $ticket->subject }}
        </div>

        <div class="card-body">

            <p><strong>Descripción:</strong></p>
            <p>{{ $ticket->description }}</p>

            <hr>

            <p><strong>Estado:</strong> 
                <span class="badge bg-primary">
                    {{ $ticket->status }}
                </span>
            </p>

            <p><strong>Cliente:</strong> {{ $ticket->creator->name }}</p>

            <p><strong>Técnico asignado:</strong> 
                {{ $ticket->technician->name ?? 'Sin asignar' }}
            </p>

            <p><strong>Creado el:</strong> 
                {{ $ticket->created_at->format('d/m/Y') }}
            </p>

            @if($ticket->closed_at)
                <p><strong>Cerrado el:</strong> 
                    {{ $ticket->closed_at->format('d/m/Y') }}
                </p>
            @endif

            <hr>

            {{-- Acciones según rol --}}
            <div class="mt-3">

                {{-- Cliente puede cerrar ticket --}}
                @if($user->role === 'cliente' && $ticket->status !== 'closed')
                    <form action="{{ route('tickets.close', $ticket) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success">
                            Cerrar Ticket
                        </button>
                    </form>
                @endif

                {{-- Técnico puede editar / cambiar estado --}}
                @if($user->role === 'tecnico')
                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">
                        Actualizar Estado
                    </a>
                @endif

                {{-- Admin puede editar o eliminar --}}
                @if($user->role === 'admin')
                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">
                        Editar Ticket
                    </a>

                    <form action="{{ route('tickets.destroy', $ticket) }}" 
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger"
                                onclick="return confirm('¿Seguro que deseas eliminar este ticket?')">
                            Eliminar
                        </button>
                    </form>
                @endif

            </div>

        </div>
    </div>

</div>
@endsection
