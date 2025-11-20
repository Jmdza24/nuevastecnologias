<div class="d-flex gap-2">

    {{-- ================================
         ADMIN: editar y eliminar
    =================================--}}
    @if ($user->role === 'admin')
        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Editar
        </a>

        <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
              onsubmit="return confirm('¿Eliminar este ticket?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </form>
    @endif


    {{-- ================================
         TÉCNICO: asignación / actualizar
    =================================--}}
    @if ($user->role === 'tecnico')

        {{-- Si NO está asignado --}}
        @if ($ticket->assigned_to === null)
            <form action="{{ route('tickets.take', $ticket) }}" method="POST">
                @csrf
                <button class="btn btn-primary btn-sm">
                    <i class="bi bi-hand-index"></i> Tomar ticket
                </button>
            </form>
        @endif

        {{-- Si está asignado a él --}}
        @if ($ticket->assigned_to === $user->id)
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Actualizar
            </a>
        @endif
    @endif


    {{-- ================================
         CLIENTE: cerrar ticket
    =================================--}}
    @if ($user->role === 'cliente' && $ticket->created_by === $user->id)
        @if ($ticket->status !== 'closed')
            <form action="{{ route('tickets.close', $ticket) }}" method="POST">
                @csrf
                <button class="btn btn-success btn-sm">
                    <i class="bi bi-check-circle"></i> Cerrar ticket
                </button>
            </form>
        @endif
    @endif

</div>
