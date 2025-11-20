@extends('layouts.app')

@section('title', 'Editar Ticket')

@section('content')
<div class="container mt-5">

    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary mb-3">Volver</a>

    <div class="card shadow-sm">
        <div class="card-header fw-bold">
            Editar Ticket #{{ $ticket->id }}
        </div>

        <div class="card-body">

            <!-- Errores -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- ADMIN PUEDE ASIGNAR TÉCNICO --}}
                @if($user->role === 'admin')
                    <div class="mb-3">
                        <label class="form-label">Asignar técnico</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Sin asignar</option>

                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}"
                                    {{ $ticket->assigned_to == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- ESTADO DEL TICKET --}}
                <div class="mb-3">
                    <label class="form-label">Estado</label>

                    <select name="status" class="form-select" required>
                        @php
                            $statuses = [
                                'open' => 'Abierto',
                                'in_progress' => 'En proceso',
                                'waiting_client' => 'Esperando respuesta del cliente',
                                'finished' => 'Terminado',
                                'closed' => 'Cerrado'
                            ];
                        @endphp

                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}"
                                {{ $ticket->status === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary">Guardar cambios</button>

            </form>

        </div>
    </div>

</div>
@endsection
